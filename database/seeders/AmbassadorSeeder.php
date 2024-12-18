<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AmbassadorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ambassadors')->insert([
            [
                'name' => 'Duta Satu',
                'code' => '01-01',
                'phone_number' => '081234567890',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Duta Dua',
                'code' => '01-02',
                'phone_number' => '082345678901',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Duta Tiga',
                'code' => '05-33',
                'phone_number' => '083456789012',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Duta Empat',
                'code' => '99-01',
                'phone_number' => '084567890123',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Duta Lima',
                'code' => '99-05',
                'phone_number' => '085678901234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
