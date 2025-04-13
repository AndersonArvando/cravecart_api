<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'name' => 'Admin',
            'npm' => '2231022',
            'dob' => '20-11-2004',
            'email' => '2231022.admin@uib.edu',
            'password' => Hash::make('qwe123'),
            'type' => '1',
            'auth_key' => '61740206648',
            'mobile' => '12345678',
            'enable' => '1',
            'image' => '',
            'description' => '',
        ]);
        User::create([
            'name' => 'Mahasiswa',
            'npm' => '2231022',
            'dob' => '20-11-2004',
            'email' => '2231022.mahasiswa@uib.edu',
            'password' => Hash::make('qwe123'),
            'type' => '3',
            'auth_key' => '61740206649',
            'mobile' => '12345678',
            'enable' => '1',
            'image' => '',
            'description' => '',
        ]);
        User::create([
            'name' => 'Kantin',
            'npm' => '2231022',
            'dob' => '20-11-2004',
            'email' => '2231022.kantin@uib.edu',
            'password' => Hash::make('qwe123'),
            'type' => '2',
            'auth_key' => '61740308497',
            'mobile' => '12345678',
            'enable' => '1',
            'image' => '',
            'description' => '',
        ]);
    }
}
