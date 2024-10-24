<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@example.com', // Unique email
                'password' => bcrypt('password123'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@example.com', // Unique email
                'password' => bcrypt('password456'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com', // Unique email
                'password' => bcrypt('password789'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@example.com', // Unique email
                'password' => bcrypt('password101'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Eve Davis',
                'email' => 'eve@example.com', // Unique email
                'password' => bcrypt('password202'), // Use a secure password
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
