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
            // Billing normal → tanggal 2 bulan berjalan
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
        $week       = (int) $this->week;   // 1–4
        $first      = (int) $this->first;

        // Ambil hari dari tanggal daftar
        $day = Carbon::parse($this->created_at)->dayOfWeek;
        // 1 = Senin, 2 = Selasa, ..., 7 = Minggu

        // Senin–Selasa
        $weeks = in_array($day, [1, 2])
            ? (5 - $week)
            : (4 - $week);

        $weeks = max(0, $weeks);

        $meetings = $weeks * 2;
        $pricePerMeeting = $hargaBulan / 8;

        $total = $meetings * $pricePerMeeting;

        if ($first === 1) {
            $total += $kit;
        }

        return (int) round($total);
    }


    public function murid()
    {
        return $this->belongsTo(Student::class, 'head', 'students', 'id');
    }
}
