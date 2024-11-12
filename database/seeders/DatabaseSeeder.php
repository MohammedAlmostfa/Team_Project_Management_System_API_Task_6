<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */

    public function run(): void
    {


        $user=User::create([

        'name' => 'Mohammed',
        'email' => 'mohmed@gmail.com',
        'password' => Hash::make(123456789),
        'role' => 'admin',
        ]);




    }
}
