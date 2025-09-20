<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User
        DB::table('users')->insert([
            'name'      => 'Test User',
            'email'     => 'test@example.com',
            'password'  => Hash::make('rahasia'),
            'nomor'     => '085',
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        // Payments
        DB::table('payments')->insert([
            [
                'name'       => 'kontrak',
                'month'      => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Reguler',
                'month'      => 11,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Kelas
        DB::table('kelas')->insert([
            [
                'name'       => 'Inclass',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Private',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Online',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Grades
        DB::table('grades')->insert([
            [
                'name'       => 'TK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Pra TK',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'SD',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Addon
        $addonId = DB::table('addons')->insertGetId([
            'name'       => 'KIT',
            'img'        => null,
            'des'        => 'Kit Pendaftaran',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Price for Addon
        DB::table('prices')->insert([
            'product'    => $addonId,
            'kelas'      => null,
            'harga'      => 10000,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Programs
        DB::table('programs')->insert([
            [
                'name'        => 'MAHI',
                'kode'        => 'MH',
                'level'       => 3,
                'des' => 'MAHI adalah salah satu program yang ada di bimbel MURIKA yang menggunakan metode Prisma Kalkulator Tangan sehingga anak dapat menghitung penjumlahan, pengurangan, perkalian, pembagian, hingga akar pangkat hanya dengan mengandalkan ataupun menggunakan sepuluh jari tangan saja.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'MABA',
                'kode'        => 'MB',
                'level'       => 5,
                'des' => 'MABA adalah program baca dan tulis menggunakan metode Abama, program MABA ini bertujuan agar anak dapat membaca dengan lancar, dapat menulis dikte dengan baik, menulis rapi, dan dapat memahami isi bacaan.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'MAPEL',
                'kode'        => 'MP',
                'level'       => 4,
                'des' => 'MAPEL atau Mahir Pelajaran merupakan program MURIKA yang bertujuan untuk memberikan materi lebih dalam pelajaran di sekolah berdasarkan tingkatannya. Dengan metode SIMPEL, Kombinasi Tips dan Trik Sederhana yang DUN, Pemahaman Konsep yang akan membuat Generasi dengan Mudah Memahami.',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'MAKOM',
                'kode'        => 'MK',
                'level'       => 4,
                'des' => 'MAKOM (Mahir Komputer) adalah program belajar inovatif yang dirancang MURIKA khusus untuk anak-anak tingkat Sekolah Dasar (SD) dengan tujuan utama untuk memberikan mereka keterampilan dasar dalam mengoperasikan komputer dan aplikasi produktivitas seperti Microsoft Word, Microsoft Excel, dan Microsoft PowerPoint. Program ini bertujuan untuk membantu mereka menjadi Mahi',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);

        // Units
        DB::table('units')->insert([
            [
                'name'       => 'Klampok',
                'addr'    => 'Klampok',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Pasarbatang',
                'addr'    => 'Pasar Batang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Pebatan',
                'addr'    => 'pebatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Pusat',
                'addr'    => 'Saditan Brebes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Bulakamba',
                'address'    => 'Bulakamba',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Losari',
                'address'    => 'Losari',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Zones
        DB::table('zones')->insert([
            [
                'name'       => 'Timur',
                'hp'      => '085',
                'pic'        => 'Syafiq',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Center',
                'hp'      => '089',
                'pic'        => 'Ilham',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Barat',
                'hp'      => '088',
                'pic'        => 'Ilham',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
