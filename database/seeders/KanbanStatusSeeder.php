<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeKanbanStatusLink;
use App\Models\KanbanStatus;
use App\Models\TaskCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KanbanStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['To Do', 'Ongoing', 'Completed'];

        foreach ($statuses as $status) {
            KanbanStatus::firstOrCreate(
                ['name' => $status]
            );
        }
        $employees = Employee::all();
        $categories = TaskCategory::all();
        foreach ($employees as $employee) {
            foreach ($categories as $category) {
                $position = 1;
                foreach (KanbanStatus::all() as $stat) {
                    EmployeeKanbanStatusLink::updateOrCreate(
                        [
                            'employee_id' => $employee->id,
                            'category_id' => $category->id,
                            'status_id' => $stat->id,
                        ],
                        [
                            'position' => $position++,
                        ]
                    );
                }
            }
        }
    }
}
