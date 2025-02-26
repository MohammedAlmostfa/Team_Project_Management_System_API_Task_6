<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();


        for ($i = 0; $i < 10; $i++) {
            Project::create([
                'name' => $faker->sentence(3), // اسم عشوائي للمشروع
                'description' => $faker->paragraph, // وصف عشوائي
                'status' => $faker->boolean, // حالة عشوائية (true/false)
            ]);
        }
    }
}
