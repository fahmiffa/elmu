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
            'name'      => 'Murika',
            'email'     => 'elmu@murika',
            'password'  => Hash::make('3lmu'),
            'status'    => 1,    
            'role'      => 0,
            'nomor'     => '08',
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }
}
