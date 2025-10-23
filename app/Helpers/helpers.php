<?php
use Illuminate\Support\Str;

if (! function_exists('convertHari')) {
    /**
     * Convert angka 1-7 jadi nama hari.
     *
     * @param int|string $number
     * @return string|null
     */
    function convertHari($number)
    {
        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        // Cast ke int untuk jaga-jaga
        $num = (int) $number;

        return $hari[$num] ?? null;
    }
}

function userName($name)
{
    $name = strtolower(str_replace(" ", "", $name));

    // Ambil 5 karakter pertama jika panjangnya lebih dari 5
    if (strlen($name) > 5) {
        $name = substr($name, 0, 5);
    }

    // Tambahkan angka acak 5 digit
    $randomNumber = str_pad(strval(random_int(0, 999)), 3, '0', STR_PAD_LEFT);

    return $name . $randomNumber;
}
