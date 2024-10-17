<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DutaSeeder extends Seeder
{
    public function run()
    {
        DB::table('dutas')->insert([
            [
                'id' => 'DUTA001',
                'name' => 'Duta Satu',
                'code' => 'DUT001',
                'phone_number' => '081234567890',
            ],
            [
                'id' => 'DUTA002',
                'name' => 'Duta Dua',
                'code' => 'DUT002',
                'phone_number' => '082345678901',
            ],
            [
                'id' => 'DUTA003',
                'name' => 'Duta Tiga',
                'code' => 'DUT003',
                'phone_number' => '083456789012',
            ],
            [
                'id' => 'DUTA004',
                'name' => 'Duta Empat',
                'code' => 'DUT004',
                'phone_number' => '084567890123',
            ],
            [
                'id' => 'DUTA005',
                'name' => 'Duta Lima',
                'code' => 'DUT005',
                'phone_number' => '085678901234',
            ],
        ]);
    }

}
