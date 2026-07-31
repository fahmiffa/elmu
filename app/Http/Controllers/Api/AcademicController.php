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
use App\Models\Schedules_students;
use App\Models\Teach;
use App\Models\Unit;
use App\Models\Permit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $da = Teach::where('user', $id)->first();
            if (!$da) return response()->json(['jadwal' => [], 'murid' => []]);

            $items = Head::where('unit', $da->unit_id)
                ->where('done', 0)
                ->with(['jadwal:id,name,day,parse,start,end', 'murid:id,name,nama_panggilan', 'murid.present' => function ($q) {
                    $q->select('id', 'unit_schedules_id', 'program_id', 'student_id', 'head_id', 'teach_id', 'hal', 'materi', 'keterangan', 'meet', 'created_at');
                }, 'murid.schedules'])
                ->get();
        } else {
            $da = Student::where('user', $id)->first();
            if (!$da) return response()->json(['jadwal' => [], 'murid' => null]);

            $items = Head::where('students', $da->id)
                ->where('done', 0)
                ->has('jadwal')
                ->with(['jadwal:id,name,day,parse,start,end', 'murid:id,name,nama_panggilan', 'murid.present' => function ($q) {
                    $q->select('id', 'unit_schedules_id', 'program_id', 'student_id', 'head_id', 'teach_id', 'hal', 'materi', 'keterangan', 'meet', 'created_at');
                }, 'murid.schedules'])
                ->get();
        }

        $allJadwal = collect();
        $allMurid = collect();
        $today = now()->toDateString();

        foreach ($items as $head) {
            if (!$head->murid) {
                continue;
            }

            $pivotData = Schedules_students::with('program')
                ->where('head', $head->id)
                ->get()
                ->keyBy('unit_schedules_id');

            $jadwalCollection = $head->jadwal;

            // 1. Hilangkan jadwal jika ada permit dengan tanggal = hari ini
            $jadwalCollection = $jadwalCollection->filter(function ($j) use ($head, $today, $pivotData) {
                $scheduleStudent = $pivotData->get($j->id);
                
                if ($scheduleStudent) {
                    $isPermitTanggal = Permit::where('student_id', $scheduleStudent->student_id ?? $head->students)
                        ->where('schedule_student_id', $scheduleStudent->id)
                        ->whereDate('tanggal', $today)
                        ->exists();
                    return !$isPermitTanggal;
                }
                
                return true;
            });

            // 2. Tambahkan jadwal jika ada permit dengan new_date = hari ini
            $permitsToday = Permit::where('student_id', $head->students)
                ->whereDate('new_date', $today)
                ->with('unitSchedule')
                ->get();

            foreach ($permitsToday as $permit) {
                if ($permit->unitSchedule) {
                    if (!$jadwalCollection->contains('id', $permit->unit_schedules_id)) {
                        $jadwalCollection->push($permit->unitSchedule);
                    }
                }
            }

            if ($jadwalCollection->isEmpty()) {
                continue; // Jika jadwal kosong, tidak perlu memproses murid ini
            }

            $jadwalWithProgram = $jadwalCollection->map(function ($j) use ($pivotData, $head) {
                $pData = $pivotData->get($j->id);
                $item = $j->toArray();
                unset($item['pivot']);
                $item['program_name'] = $pData?->program?->name ?? $head->programs->name ?? null;
                $item['program_id']    = $pData?->program_id ?? $head->program;
                return $item;
            });

            $allJadwal = $allJadwal->merge($jadwalWithProgram);

            if ($role == 3) {
                $m = $head->murid->toArray();
                $m['program_id'] = $head->program;

                // Update schedules array on murid object based on permit
                if (isset($m['schedules'])) {
                    $m['schedules'] = array_filter($m['schedules'], function($sched) use ($today) {
                        $isPermitTanggal = Permit::where('student_id', $sched['student_id'])
                            ->where('schedule_student_id', $sched['id'])
                            ->whereDate('tanggal', $today)
                            ->exists();
                        return !$isPermitTanggal;
                    });
                    
                    foreach ($permitsToday as $permit) {
                        if ($permit->unitSchedule) {
                            $m['schedules'][] = [
                                'id' => $permit->schedule_student_id,
                                'head' => $head->id,
                                'unit_schedules_id' => $permit->unit_schedules_id,
                                'student_id' => $permit->student_id,
                                'program_id' => $head->program,
                            ];
                        }
                    }
                    $m['schedules'] = array_values($m['schedules']);
                }

                // Check which schedules this student already was present TODAY for THIS HEAD
                $presentSchedules = DB::table('student_presents')
                    ->where('student_id', $head->students)
                    ->where('head_id', $head->id)
                    ->whereDate('created_at', $today)
                    ->pluck('unit_schedules_id')
                    ->toArray();

                $m['absen'] = empty($presentSchedules) ? 0 : 1;
                $m['absen_hari_ini'] = $presentSchedules;
                $allMurid->push($m);
            }
        }

        if ($role == 3) {
            return response()->json([
                'jadwal' => $allJadwal->unique(function ($item) {
                    return $item['id'] . '-' . $item['program_id'];
                })->values()->all(),
                'murid' => $allMurid->unique(function ($item) {
                    return $item['id'] . '-' . $item['program_id'];
                })->values()->all(),
            ]);
        } else {
            $da->load('present', 'schedules');
            return response()->json([
                'jadwal' => $allJadwal->unique(function ($item) {
                    return $item['id'] . '-' . $item['program_id'];
                })->values()->all(),
                'murid' => $da,
            ]);
        }
    }

    public function present()
    {
        $role = JWTAuth::user()->role;
        $id   = JWTAuth::user()->id;
        
        if ($role == 3) {
            $da = Teach::where('user', $id)->first();
            if (!$da) return response()->json(['items' => []]);

            $items = StudentPresent::where('teach_id', $da->id)
                ->with(['student:id,name,nama_panggilan', 'unitSchedule:id,name', 'program:id,name', 'reg.units:id,name'])
                ->latest()
                ->get()
                ->map(function ($item) {
                    return [
                        'id_siswa'     => $item->student->id ?? null,
                        'nama_siswa'   => $item->student->name ?? null,
                        'nama_panggilan' => $item->student->nama_panggilan ?? null,
                        'nama_sessi'   => $item->unitSchedule->name ?? null,
                        'id_program'   => $item->program->id ?? null,
                        'nama_program' => $item->program->name ?? null,
                        'id_unit'      => $item->reg->units->id ?? null,
                        'nama_unit'    => $item->reg->units->name ?? null,
                        'hal'          => $item->hal,
                        'materi'       => $item->Materi,
                        'keterangan'   => $item->Keterangan,
                        'meet'         => $item->meet,
                        'tanggal'      => $item->tanggal,
                        'present'      => isset($item->present) ? (bool)$item->present : true,
                    ];
                });
        } else {
            $da = Student::where('user', $id)->first();
            if (!$da) return response()->json(['items' => []]);

            $items = StudentPresent::where('student_id', $da->id)
                ->with(['guru:id,name', 'unitSchedule:id,name', 'program:id,name', 'reg.units:id,name'])
                ->latest()
                ->get()
                ->map(function ($item) {
                    return [
                        'nama_sessi'   => $item->unitSchedule->name ?? null,
                        'id_program'   => $item->program->id ?? null,
                        'nama_program' => $item->program->name ?? null,
                        'id_unit'      => $item->reg->units->id ?? null,
                        'nama_unit'    => $item->reg->units->name ?? null,
                        'hal'          => $item->hal,
                        'materi'       => $item->Materi,
                        'keterangan'   => $item->Keterangan,
                        'meet'         => $item->meet,
                        'tanggal'      => $item->tanggal,
                        'nama_guru'    => $item->guru->name ?? null,
                        'present'      => isset($item->present) ? (bool)$item->present : true,
                    ];
                });
        }

        return response()->json(['items' => $items]);
    }

    public function UpJadwal(Request $request)
    {
        $currentHour = (int) date('H');
        if ($currentHour < 6 || $currentHour >= 21) {
            return response()->json([
                'errors' => ['message' => ['Absensi hanya dapat diisi pada jam 06:00 pagi sampai 21:00 malam.']]
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'jadwal' => 'required',
            'user'   => 'required|array',
        ], [
            'jadwal.required' => 'Jadwal wajib diisi.',
            'user.required'   => 'Siswa wajib dipilih.',
            'user.array'      => 'Siswa harus dalam format array.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        // 1. Verify if the Unit Schedule exists
        $scheduleExists = DB::table('unit_schedules')->where('id', $request->jadwal)->exists();
        if (!$scheduleExists) {
            return response()->json([
                'errors' => ['jadwal' => ['Jadwal tidak ditemukan atau tidak valid.']]
            ], 400);
        }

        $today     = now()->toDateString();
        $users     = $request->user;
        $processed = 0;
        $failed    = [];

        $isPresentInput = filter_var($request->input('present', true), FILTER_VALIDATE_BOOLEAN);

        foreach ($users as $studentId) {
            // 2. Get head_id mapping for this student and schedule combination
            $mappingQuery = Schedules_students::where('student_id', $studentId)
                ->where('unit_schedules_id', $request->jadwal);

            if ($request->program_id) {
                $mappingQuery->where('program_id', $request->program_id);
            }

            $mapping = $mappingQuery->first();

            if ($mapping) {
                $alreadyExists = StudentPresent::where('student_id', $studentId)
                    ->whereDate('created_at', $today)
                    ->where('unit_schedules_id', $request->jadwal)
                    ->where('head_id', $mapping->head)
                    ->exists();

                if (!$alreadyExists) {
                    // Hitung urutan meet (pertemuan ke-x) untuk student ini
                    $meetCount = StudentPresent::where('student_id', $studentId)->count();

                    $present                    = new StudentPresent;
                    $present->student_id        = $studentId;
                    $present->unit_schedules_id = $request->jadwal;
                    $present->head_id           = $mapping->head;
                    $present->program_id        = $mapping->program_id;
                    // Safely get teach_id if data exists
                    $present->teach_id          = optional(JWTAuth::user()->data)->id;
                    $present->present           = $isPresentInput;
                    $present->hal               = $isPresentInput ? $request->hal : null;
                    $present->Materi            = $isPresentInput ? $request->Materi : null;
                    $present->Keterangan        = $isPresentInput ? $request->Keterangan : $request->Keterangan;
                    $present->meet              = $meetCount + 1;
                    $present->save();

                    $processed++;
                } else {
                    $processed++; // Consider already exists as correctly handled for this student
                }
            } else {
                $studentName = DB::table('students')->where('id', $studentId)->value('name') ?? "ID $studentId";
                $failed[] = "Siswa $studentName tidak terdaftar dalam jadwal ini.";
            }
        }

        // 3. Return results based on success/failure
        if (count($failed) > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Beberapa siswa gagal diproses.',
                'errors'  => $failed
            ], 400);
        }

        if ($processed == 0 && count($users) > 0) {
            return response()->json([
                'status'  => false,
                'message' => 'Tidak ada data siswa yang valid untuk diproses.',
            ], 400);
        }

        return response()->json(['status' => true, 'message' => "$processed data berhasil diproses."], 200);
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
