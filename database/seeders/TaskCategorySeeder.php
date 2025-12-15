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
            ['name' => 'Personal', 'color' => 'purple'],
            ['name' => 'Project', 'color' => 'green'],
            ['name' => 'Organization', 'color' => 'orange'],
        ];
        foreach ($categories as $category) {
            TaskCategory::updateOrCreate(
                ['category_name' => $category['name']],
                ['color' => $category['color']]
            );
        }
    }
}
