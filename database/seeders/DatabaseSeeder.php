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
            'status'    => 1,    
            'nomor'     => '085',
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);
    }
}
