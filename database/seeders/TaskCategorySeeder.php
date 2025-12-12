<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Personal',
            'Project',
            'Organization',
        ];
        foreach ($categories as $name) {
            TaskCategory::firstOrCreate([
                'category_name' => $name
            ]);
        }
    }
}
