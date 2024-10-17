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
                'id' => 'BENEF001',
                'name' => 'Beneficiary A',
                'date_of_birth' => '2010-01-01',
                'age' => 14,
                'address' => 'Address A',
                'father_name' => 'Father A',
                'mother_name' => 'Mother A',
                'school_level' => 'SD',
                'school_name' => 'Elementary School A',
                'shirt_size' => 'M',
                'shoe_size' => '36',
            ],
            [
                'id' => 'BENEF002',
                'name' => 'Beneficiary B',
                'date_of_birth' => '2011-05-01',
                'age' => 13,
                'address' => 'Address B',
                'father_name' => 'Father B',
                'mother_name' => 'Mother B',
                'school_level' => 'SD',
                'school_name' => 'Elementary School B',
                'shirt_size' => 'L',
                'shoe_size' => '37',
            ],
            [
                'id' => 'BENEF003',
                'name' => 'Beneficiary C',
                'date_of_birth' => '2012-10-01',
                'age' => 12,
                'address' => 'Address C',
                'father_name' => 'Father C',
                'mother_name' => 'Mother C',
                'school_level' => 'SMP',
                'school_name' => 'Middle School C',
                'shirt_size' => 'M',
                'shoe_size' => '38',
            ],
            [
                'id' => 'BENEF004',
                'name' => 'Beneficiary D',
                'date_of_birth' => '2013-03-01',
                'age' => 11,
                'address' => 'Address D',
                'father_name' => 'Father D',
                'mother_name' => 'Mother D',
                'school_level' => 'SD',
                'school_name' => 'Elementary School D',
                'shirt_size' => 'S',
                'shoe_size' => '35',
            ],
            [
                'id' => 'BENEF005',
                'name' => 'Beneficiary E',
                'date_of_birth' => '2014-07-01',
                'age' => 10,
                'address' => 'Address E',
                'father_name' => 'Father E',
                'mother_name' => 'Mother E',
                'school_level' => 'TK',
                'school_name' => 'Kindergarten E',
                'shirt_size' => 'XS',
                'shoe_size' => '34',
            ],
        ]);
    }
}
