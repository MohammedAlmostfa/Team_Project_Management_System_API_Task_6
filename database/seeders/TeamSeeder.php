<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // العثور على المشروع الأول (يمكن تغيير الشرط حسب الحاجة)
        $project = Project::findOrFail(1);

        // العثور على مستخدمين عشوائيين لأدوار المطورين
        $developers = User::where('role', 'user')->take(3)->get();
        $manager = User::where('role', 'admin')->first();
        $tester = User::where('role', 'user')->inRandomOrder()->first();

        // ربط المطورين بالمشروع
        foreach ($developers as $developer) {
            $project->users()->attach($developer->id, ['role' => 'Developer']);
        }

        // ربط المدير بالمشروع
        if ($manager) {
            $project->users()->attach($manager->id, ['role' => 'Manager']);
        }

        // ربط الفاحص بالمشروع
        if ($tester) {
            $project->users()->attach($tester->id, ['role' => 'Tester']);
        }
    }
}
