<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $user=User::create([

        'name' => 'Mohammed Almostfa',
        'email' => 'mohmedalmostfa36@gmail.com',
        'password' => Hash::make(123456789),
        'role' => 'admin',
        ]);

        $user=User::create([

        'name' => 'Ali Almostfa',
        'email' => 'AliAlmostfa@gmail.com',
        'password' => Hash::make(123456789),
        'role' => 'user',
        ]);

        $user=User::create([

        'name' => 'kaiss Almohammed',
        'email' => 'Kaissalmohammed@gmail.com',
        'password' => Hash::make(123456789),
        'role' => 'user',
        ]);

        $user=User::create([

        'name' => 'Issa Almostfa',
        'email' => 'Issaalmostfa@gmail.com',
        'password' => Hash::make(123456789),
        'role' => 'user',
        ]);



    }
}
