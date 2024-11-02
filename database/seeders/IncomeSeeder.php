<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IncomeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('incomes')->insert([
            [
                'transfer_date' => '2024-10-01',
                'amount' => 100000,
                'donor' => 'Donor A',
                'team' => '1',
                'payment_method' => 'Bank Transfer',
                'type' => 'Donation',
            ],
            [
                'transfer_date' => '2024-10-02',
                'amount' => 200000,
                'donor' => 'Donor B',
                'team' => '2',
                'payment_method' => 'Cash',
                'type' => 'Sponsorship',
            ],
            [
                'transfer_date' => '2024-10-03',
                'amount' => 150000,
                'donor' => 'Donor C',
                'team' => '3',
                'payment_method' => 'Credit Card',
                'type' => 'Donation',
            ],
            [
                'transfer_date' => '2024-10-04',
                'amount' => 250000,
                'donor' => 'Donor D',
                'team' => '4',
                'payment_method' => 'Bank Transfer',
                'type' => 'Sponsorship',
            ],
            [
                'transfer_date' => '2024-10-05',
                'amount' => 300000,
                'donor' => 'Donor E',
                'team' => '4',
                'payment_method' => 'Cash',
                'type' => 'Donation',
            ],
        ]);
    }
}
