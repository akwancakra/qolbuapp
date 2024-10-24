<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        DB::table('transactions')->insert([
            [
                'id' => 'TRANS001',
                'created_at' => now(),
                'updated_at' => now(),
                'transfer_date' => '2024-10-01',
                'donor_name' => 'Donor A',
                'method' => 'Bank Transfer',
                'type' => 'Donation',
                'amount' => 100000,
            ],
            [
                'id' => 'TRANS002',
                'created_at' => now(),
                'updated_at' => now(),
                'transfer_date' => '2024-10-02',
                'donor_name' => 'Donor B',
                'method' => 'Cash',
                'type' => 'Sponsorship',
                'amount' => 200000,
            ],
            [
                'id' => 'TRANS003',
                'created_at' => now(),
                'updated_at' => now(),
                'transfer_date' => '2024-10-03',
                'donor_name' => 'Donor C',
                'method' => 'Credit Card',
                'type' => 'Donation',
                'amount' => 150000,
            ],
            [
                'id' => 'TRANS004',
                'created_at' => now(),
                'updated_at' => now(),
                'transfer_date' => '2024-10-04',
                'donor_name' => 'Donor D',
                'method' => 'Bank Transfer',
                'type' => 'Sponsorship',
                'amount' => 250000,
            ],
            [
                'id' => 'TRANS005',
                'created_at' => now(),
                'updated_at' => now(),
                'transfer_date' => '2024-10-05',
                'donor_name' => 'Donor E',
                'method' => 'Cash',
                'type' => 'Donation',
                'amount' => 300000,
            ],
        ]);
    }
}
