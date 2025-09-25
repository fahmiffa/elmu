<?php

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
