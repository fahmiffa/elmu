<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Head;
use App\Models\Paid;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class BillingController extends Controller
{
    public function payment()
    {
        $item = Payment::latest()->get();
        return response()->json($item);
    }

    public function bill()
    {
        $id   = JWTAuth::user()->id;
        $role = JWTAuth::user()->role;

        $res = Student::select('id', 'name', 'gender', 'user')
            ->with(
                'reg:id,students,price,unit,number,program',
                'reg.product:id,harga,product,kelas',
                'reg.product.class:id,name',
                'reg.product.program:id,name',
                'reg.bill:id,head,time,bulan,tahun,via,status,first',
                'reg.lay.product.item'
            );

        if ($role == 2) {
            $student = $res->where('user', $id)->first();
            foreach ($student->reg as $val) {

                $bill = $val->bill->map(function ($a) use ($val) {
                    return [
                        "tipe"       => 0,
                        "id"         => $a->id,
                        'price'      => (int) $val->product->harga,
                        'total'      => $a->total,
                        "keterangan" => "Tagihan Bulan " . $a->bulan,
                        "kit"        => $a->kit,
                        "status"     => $a->status,
                        "via"        => $a->via,
                    ];
                })->toArray();

                $lay = $val->lay->map(function ($a) {
                    return [
                        "id"         => $a->id,
                        "tipe"       => 1,
                        'price'      => (int) $a->product->harga,
                        'total'      => (int) $a->product->harga,
                        "keterangan" => $a->product->item->name,
                        "kit"        => null,
                        "status"     => $a->status,
                        "via"        => $a->via,
                    ];
                })->toArray();

                $item[] = [
                    "program" => $val->product->program->name,
                    "kelas"   => $val->product->class->name,
                    "bill"    => array_merge($bill, $lay),
                ];
            }

            $result = [
                'name' => $student->name,
                "data" => $item,
            ];
            return response()->json($result);
        }

        if ($role == 3) {
            $guru     = Teach::where('user', $id)->first();
            $unit     = $guru->unit_id;
            $students = $res->whereHas('reg.units', function ($q) use ($unit) {
                $q->where('unit', $unit)->where('done', 0);
            })->get();

            $grouped = [];
            foreach ($students as $student) {
                foreach ($student->reg as $reg) {
                    if ($reg->done == 0) {
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

                        $bills = collect($reg->bill)->map(function ($bill) use ($reg) {
                            return [
                                'total'      => $bill->total,
                                'price'      => (int) $reg->product->harga,
                                'status'     => $bill->status,
                                "keterangan" => "Tagihan Bulan " . $bill->bulan,
                                "kit"        => $bill->kit,
                            ];
                        })->toArray();

                        $lay = collect($reg->lay)->map(function ($a) {
                            return [
                                'price'      => (int) $a->product->harga,
                                'total'      => (int) $a->product->harga,
                                "keterangan" => $a->product->item->name,
                                "kit"        => null,
                                "status"     => $a->status,
                            ];
                        })->toArray();

                        $grouped[$key]['students'][] = [
                            'name'  => $student->name,
                            'bills' => array_merge($bills, $lay),
                        ];
                    }
                }
            }
            return response()->json(array_values($grouped));
        }
    }

    public function billStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bill' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 400);
        }

        $paid         = Paid::where('id', $request->bill)->firstOrFail();
        $paid->status = 1;
        $paid->time   = date("Y-m-d H:i:s");
        $paid->ket    = $request->ket;
        $paid->via    = $request->via;
        $paid->save();

        return response()->json([
            'status' => true,
        ]);
    }

    public function tagihan()
    {
        $id = JWTAuth::user()->id;

        $student = Student::where('user', $id)
            ->with('reg.bill', 'reg.lay.product.item')
            ->first();

        $bill          = [];
        $hasStatusZero = false;

        if ($student) {
            foreach ($student->reg as $val) {

                foreach ($val->bill as $a) {
                    if ($a->status != 1) {
                        $hasStatusZero = true;
                        $bill[]        = [
                            'total'      => (int) $a->total,
                            "keterangan" => "Anda punya tagihan bulan " . $a->bulan,
                            "status"     => $a->status,
                        ];
                    }
                }

                foreach ($val->lay as $a) {
                    if ($a->status != 1) {
                        $hasStatusZero = true;
                        $bill[]        = [
                            'total'      => (int) $a->product->harga,
                            "keterangan" => "Anda punya tagihan " . $a->product->item->name,
                            "status"     => $a->status,
                        ];
                    }
                }
            }
        }

        if (! $hasStatusZero) {
            $bill = [];
        }
        return response()->json($bill);
    }
}
