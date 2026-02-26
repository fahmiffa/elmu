<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Paid extends Model
{
    protected $appends = ['tempo', 'kit', 'total', 'tipe'];

    public function reg()
    {
        return $this->belongsTo(Head::class, 'head', 'id');
    }

    public function gettempoAttribute()
    {
        if ($this->first == 1) {
            // Billing pertama → created_at + 10 hari
            $date = Carbon::parse($this->created_at)
                ->addDays(10)
                ->locale('id');
            return $date->translatedFormat('l, d F Y');
        } else {
            $date = Carbon::parse($this->created_at)
                // ->addMonthNoOverflow() // bulan berikutnya
                ->day(2)
                ->locale('id');
            return $date->translatedFormat('l, d F Y');
        }
    }

    public function gettipeAttribute()
    {
        if ($this->status == 1) {
            if ($this->via == "cash") {
                return "Offline";
            } else {
                return "Online";
            }
        } else {
            return null;
        }
    }

    public function getkitAttribute()
    {
        return $this->first == 1 ? [
            "id"    => $this->reg->programs->id,
            "name"  => "Stater KIT " . $this->reg->programs->name,
            "des"   => $this->reg->programs->kit_des,
            "price" => [
                "product" => $this->reg->programs->id,
                "harga"   => $this->reg->programs->kit,
            ],

        ] : null;
    }


public function getTotalAttribute()
{
    $hargaBulan = (int) $this->reg->prices->harga;
    $kit        = (int) $this->reg->programs->kit;
    $first      = (int) $this->first;

    // Hari daftar (WIB)
    $created = Carbon::parse($this->created_at)->timezone('Asia/Jakarta');
    $day = $created->dayOfWeekIso; // 1 = Senin ... 7 = Minggu

    // Minggu ke berapa dalam bulan (1–4)
    $week = min(4, $created->weekOfMonth);

    // Sisa minggu termasuk minggu ini
    $weeksRemaining = 4 - $week + 1;

    // Hitung pertemuan sesuai hari daftar
    if (in_array($day, [1, 2])) {
        // Senin / Selasa → dapat full minggu
        $meetings = $weeksRemaining * 2;
    } else {
        // Rabu–Minggu → minggu ini dikurangi 1
        $meetings = max(0, $weeksRemaining * 2 - 2);
    }

    // Harga per pertemuan
    $pricePerMeeting = $hargaBulan / 8;

    // Total biaya kelas
    $total = $meetings * $pricePerMeeting;

    // Tambah kit jika pendaftaran pertama
    if ($first === 1) {
        $total += $kit;
        return (int) round($total);
    }
    else
    {
        return $hargaBulan;
    }

}



    public function murid()
    {
        return $this->belongsTo(Student::class, 'head', 'students', 'id');
    }
}
