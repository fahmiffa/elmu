<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Unit;
use App\Models\UnitKelas;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('rahasia'),
            'nomor' => '085',
        ]);

        Kelas::factory()->create([
            'name'     => 'Inclass',
        ]);

        Kelas::factory()->create([
            'name'     => 'Private',
        ]);

        Kelas::factory()->create([
            'name'     => 'Online',
        ]);


    }
}
