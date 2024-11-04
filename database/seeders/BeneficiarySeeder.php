<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BeneficiarySeeder extends Seeder
{
    public function run()
    {
        DB::table('beneficiaries')->insert([
            [
                'nik' => '3201010101010001',
                'name' => 'Beneficiary A',
                'place_of_birth' => 'Place of Birth A',
                'date_of_birth' => '2010-01-01',
                'gender' => 'P',
                'neighborhood_unit' => '01/04',
                'father_name' => 'Father A',
                'mother_name' => 'Mother A',
                'education_level' => 'SD',
                'school_grade' => '1',
                'shirt_size' => 'M',
                'shoe_size' => '36',
            ],
            [
                'nik' => '3201020202020002',
                'name' => 'Beneficiary B',
                'place_of_birth' => 'Place of Birth B',
                'date_of_birth' => '2011-05-01',
                'gender' => 'L',
                'neighborhood_unit' => '01/04',
                'father_name' => 'Father B',
                'mother_name' => 'Mother B',
                'education_level' => 'SD',
                'school_grade' => '2',
                'shirt_size' => 'L',
                'shoe_size' => '37',
            ],
            [
                'nik' => '3201030303030003',
                'name' => 'Beneficiary C',
                'place_of_birth' => 'Place of Birth C',
                'date_of_birth' => '2012-10-01',
                'gender' => 'P',
                'neighborhood_unit' => '02/04',
                'father_name' => 'Father C',
                'mother_name' => 'Mother C',
                'education_level' => 'SMP',
                'school_grade' => '3',
                'shirt_size' => 'M',
                'shoe_size' => '38',
            ],
            [
                'nik' => '3201040404040004',
                'name' => 'Beneficiary D',
                'place_of_birth' => 'Place of Birth D',
                'date_of_birth' => '2013-03-01',
                'gender' => 'L',
                'neighborhood_unit' => '02/04',
                'father_name' => 'Father D',
                'mother_name' => 'Mother D',
                'education_level' => 'SD',
                'school_grade' => '4',
                'shirt_size' => 'S',
                'shoe_size' => '35',
            ],
            [
                'nik' => '3201101010100010',
                'name' => 'Beneficiary E',
                'place_of_birth' => 'Place of Birth E',
                'date_of_birth' => '2014-07-01',
                'gender' => 'P',
                'neighborhood_unit' => '01/03',
                'father_name' => 'Father E',
                'mother_name' => 'Mother E',
                'education_level' => 'TK',
                'school_grade' => '1',
                'shirt_size' => 'XS',
                'shoe_size' => '34',
            ],
        ]);
    }
}
