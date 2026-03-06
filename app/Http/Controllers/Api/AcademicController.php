<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Head;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Materi;
use App\Models\Price;
use App\Models\Program;
use App\Models\Raport;
use App\Models\Student;
use App\Models\StudentPresent;
use App\Models\Teach;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AcademicController extends Controller
{
    public function program()
    {
        $role     = JWTAuth::user()->role;
        $products = Program::select('id', 'name', 'des', 'level')->whereNull('extend')->get();
        if ($role == 3) {
            $products = $products->map(function ($item) {
                $item->aktif = 0;
                return $item;
            });
            return response()->json([
                'items' => $products,
            ]);
        } else {
            $items = JWTAuth::user()->data->reg->map(function ($q) {
                return $q->programs;
            });
            $programIds = $items->pluck("id")->toArray();
            $products   = $products->map(function ($item) use ($programIds) {
                $item->aktif = in_array($item->id, $programIds) ? 1 : 0;
                return $item;
            });
        }
        return response()->json([
            'items' => $products,
        ]);
    }

    public function kelas()
    {
        $products = Kelas::select('id', 'name')
            ->with([
                'units:id,name',
                'program' => function ($q) {
                    $q->where(function ($query) {
                        $query->whereNull('extend')->orWhere('extend', 0)->orWhere('extend', false);
                    });
                }
            ])
            ->get()
            ->each(function ($items) {
                $items->units->each->makeHidden('pivot');
                $items->program->each->makeHidden(['pivot', 'created_at', 'updated_at', 'deleted_at', 'des', 'level', 'extend']);
            });
        $grades = Grade::select('id', 'name')->get();
        return response()->json(['items' => $products, 'grade' => $grades]);
    }

    public function unit()
    {
        $products = Unit::select('id', 'name')->get();
        return response()->json(['items' => $products]);
    }

    public function price($kelas, $product)
    {
        $id       = JWTAuth::user()->id;
        $products = Price::select('id', 'harga', 'product', 'kelas')
            ->where('kelas', $kelas)
            ->where('product', $product)
            ->with(['class:id,name', 'program:id,name'])
            ->get();
        return response()->json(['items' => $products]);
    }

    public function level()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        $res = Student::select('id', 'name', 'gender', 'user')
            ->with(
                'reg:id,students,price,unit,number,program',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                'reg.level',
            );

        if ($role == 2) {
            $head = Head::whereHas('murid', function ($q) use ($id) {
                $q->where('user', $id);
            })
                ->with('level', 'class')
                ->get();

            foreach ($head as $val) {
                $da[] = [
                    "program" => $val->programs->name,
                    "kelas"   => $val->class->name,
                    "level"   => $val->level->select('id', "level", "status", "note")->toArray(),
                ];
            }
            return response()->json($da);
        }

        if ($role == 3) {
            $guru     = Teach::where('user', $id)->first();
            $unit     = $guru->unit_id;
            $students = $res->whereHas('reg.units', function ($q) use ($unit) {
                $q->where('unit', $unit);
            })->get();

            $grouped = [];

            foreach ($students as $student) {
                foreach ($student->reg as $reg) {
                    $programName = $reg->programs->name ?? 'Unknown Program';
                    $className   = $reg->units->kelas[0]->name ?? 'Unknown Class';

                    $key = $programName . '|' . $className;

                    if (! isset($grouped[$key])) {
                        $grouped[$key] = [
                            'program'  => $programName,
                            'class'    => $className,
                            'students' => [],
                        ];
                    }

                    $levels = collect($reg->level)->map(function ($item) {
                        return [
                            'level'  => $item->level,
                            'status' => $item->status,
                            'note'   => $item->note,
                        ];
                    })->toArray();

                    $grouped[$key]['students'][] = [
                        'head'  => $reg->id,
                        'id'    => $student->id,
                        'name'  => $student->name,
                        'name'  => $student->name,
                        'level' => $levels,
                    ];
                }
            }

            return response()->json(array_values($grouped));
            return response()->json($students);
        }
    }

    public function Uplevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user'  => 'required',
            'head'  => 'required',
            'level' => 'required',
        ], [
            'user.required'  => 'Tidak murid yang dipilih',
            'head.required'  => 'Head diperlukan',
            'level.required' => 'Level diperlukan',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $levelsQuery = Level::where('student_id', $request->user)
            ->where('head', $request->head);

        if (! $levelsQuery->exists()) {
            return response()->json(['errors' => ['message' => 'Murid tidak valid']], 400);
        }

        $upgradeInProcess = Level::where('student_id', $request->user)
            ->where('head', $request->head)
            ->where('status', 0)
            ->exists();

        if ($upgradeInProcess) {
            return response()->json(['errors' => ['message' => 'Murid dalam proses Upgrade']], 400);
        }

        $currentLevel = $levelsQuery->latest()->first();
        if (! $currentLevel) {
            return response()->json(['errors' => ['message' => 'Murid tidak valid', 'data' => $levelsQuery->first()]], 400);
        }

        if ($currentLevel->level >= $request->level) {
            return response()->json(['errors' => ['message' => 'Level Tidak Valid']], 400);
        }

        $newLevel             = new Level();
        $newLevel->student_id = $currentLevel->student_id;
        $newLevel->teach_user = JWTAuth::user()->id;
        $newLevel->level      = $request->level;
        $newLevel->head       = $request->head;
        $newLevel->note       = $request->note ?? null;
        $newLevel->save();

        return response()->json(['status' => true], 200);
    }

    public function jadwal()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        if ($role == 3) {
            $da        = Teach::where('user', $id)->first();
            $items     = Head::where('unit', $da->unit_id)
                ->where('done', 0)
                ->with(['jadwal:id,name,day,parse,start,end', 'murid:id,name', 'murid.present'])
                ->get();

            $allJadwal = collect();
            $allMurid  = collect();

            foreach ($items as $head) {
                // Get program names for each schedule for this head
                $pivotData = \DB::table('schedules_students')
                    ->join('programs', 'schedules_students.program_id', '=', 'programs.id')
                    ->where('schedules_students.head', $head->id)
                    ->select('programs.name as program_name', 'schedules_students.unit_schedules_id')
                    ->get()
                    ->keyBy('unit_schedules_id');

                $jadwalWithProgram = $head->jadwal->map(function ($j) use ($pivotData) {
                    $item = $j->toArray();
                    unset($item['pivot']);
                    $item['program_name'] = $pivotData[$j->id]->program_name ?? null;
                    return $item;
                });

                $allJadwal = $allJadwal->merge($jadwalWithProgram);
                $allMurid->push($head->murid);
            }

            return response()->json([
                'jadwal' => $allJadwal->values()->unique('id')->all(),
                'murid'  => $allMurid->values()->unique('id')->all(),
            ]);
        } else {
            $da    = Student::where('user', $id)->first();
            $items = Head::where('students', $da->id)
                ->has('jadwal')
                ->with(['jadwal:id,name,day,parse,start,end', 'murid:id,name', 'murid.present'])
                ->first();

            if ($items) {
                $pivotData = \DB::table('schedules_students')
                    ->join('programs', 'schedules_students.program_id', '=', 'programs.id')
                    ->where('schedules_students.head', $items->id)
                    ->select('programs.name as program_name', 'schedules_students.unit_schedules_id')
                    ->get()
                    ->keyBy('unit_schedules_id');

                $jadwalWithProgram = $items->jadwal->map(function ($j) use ($pivotData) {
                    $item = $j->toArray();
                    unset($item['pivot']);
                    $item['program_name'] = $pivotData[$j->id]->program_name ?? null;
                    return $item;
                });

                return response()->json([
                    'jadwal' => $jadwalWithProgram,
                    'murid'  => $items->murid,
                ]);
            } else {
                return response()->json([
                    'jadwal' => [],
                    'murid'  => [],
                ]);
            }
        }
    }

    public function UpJadwal(Request $request)
    {
        $id = JWTAuth::user()->id;

        $validator = Validator::make($request->all(), [
            'jadwal' => 'required',
            'user'   => 'required',
        ], [
            'jadwal.required' => 'Jadwal wajib diisi.',
            'user.required'   => 'TIdak murid yang di pilih',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $today = now()->toDateString();

        $user = $request->user;
        for ($i = 0; $i < count($user); $i++) {

            $alreadyExists = StudentPresent::where('student_id', $user[$i])
                ->whereDate('created_at', $today)
                ->where('unit_schedules_id', $request->jadwal)
                ->exists();
            if (! $alreadyExists) {
                $present                    = new StudentPresent;
                $present->student_id        = $user[$i];
                $present->unit_schedules_id = $request->jadwal;
                $present->teach_id          = JWTAuth::user()->data->id;
                $present->hal               = $request->hal;
                $present->Materi            = $request->Materi;
                $present->Keterangan        = $request->Keterangan;
                $present->save();
            }
        }

        return response()->json(['status' => true], 200);
    }

    public function raport()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;
        if ($role == 2) {
            $item = Raport::with('murid')->where('student_id', $id)->latest()->get()
                ->map(function ($q) {
                    return ['name' => $q->name, "url" => asset('storage/' . $q->file), 'murid' => $q->murid->name];
                });
        } else {
            $murid = JWTAuth::user()->data->murid->pluck("students")->toArray();
            $murid = Student::whereIn("id", $murid)->pluck("user")->toArray();
            $item  = Raport::whereIn('student_id', $murid)->with('murid')->latest()->get()
                ->map(function ($q) {
                    return ['name' => $q->name, "url" => asset('storage/' . $q->file), 'murid' => $q->murid->name];
                });
        }
        return response()->json($item);
    }

    public function materi()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;
        if ($role == 2) {
            $da = JWTAuth::user()->data->program->pluck("id")->toArray();
        } else {
            $da = JWTAuth::user()->data->head->pluck("program")->toArray();
        }
        $items = Materi::whereIn('program_id', $da)->with('program')->latest()->get()
            ->map(function ($q) {
                return [
                    "id"   => $q->id,
                    "name" => $q->program->name,
                    "pdf"  => $q->pdf,
                ];
            });

        return response()->json($items);
    }
}
