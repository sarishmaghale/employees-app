<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use App\Models\TaskCategoryLink;
use App\Models\TaskSubCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subCategories = [
            ['name' => 'Work'],
            ['name' => 'Meeting'],
            ['name' => 'Important'],
            ['name' => 'Misc'],
            ['name' => 'Appointment']
        ];
        foreach ($subCategories as $sub) {
            TaskSubCategory::updateOrCreate(
                ['sub_category_name' => $sub['name']]
            );
        }

        $personal = TaskCategory::where('category_name', 'Personal')->first();
        $project = TaskCategory::where('category_name', 'Project')->first();
        $org = TaskCategory::where('category_name', 'Organization')->first();

        $work = TaskSubCategory::where('sub_category_name', 'Work')->first();
        $meeting = TaskSubCategory::where('sub_category_name', 'Meeting')->first();
        $imp = TaskSubCategory::where('sub_category_name', 'Important')->first();
        $misc = TaskSubCategory::where('sub_category_name', 'Misc')->first();
        $apt = TaskSubCategory::where('sub_category_name', 'Appointment')->first();

        $links = [
            ['category_id' => $personal->id, 'sub_category_id' => $work->id],
            ['category_id' => $personal->id, 'sub_category_id' => $misc->id],
            ['category_id' => $personal->id, 'sub_category_id' => $imp->id],
            ['category_id' => $personal->id, 'sub_category_id' => $apt->id],
            ['category_id' => $project->id, 'sub_category_id' => $work->id],
            ['category_id' => $project->id, 'sub_category_id' => $meeting->id],
            ['category_id' => $project->id, 'sub_category_id' => $imp->id],
            ['category_id' => $org->id, 'sub_category_id' => $meeting->id],
            ['category_id' => $org->id, 'sub_category_id' => $imp->id],
            ['category_id' => $org->id, 'sub_category_id' => $work->id],
        ];
        foreach ($links as $link) {
            TaskCategoryLink::updateOrCreate([
                'category_id' => $link['category_id'],
                'sub_category_id' => $link['sub_category_id']
            ]);
        }
    }
}
