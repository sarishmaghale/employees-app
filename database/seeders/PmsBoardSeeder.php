<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\PmsBoard;
use App\Models\PmsCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PmsBoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boardCreator = Employee::where('email', 'admin@example.com')->first();
        $boardMember = Employee::where('email', 'sg@example.com')->first();

        $board = PmsBoard::create([
            'board_name' => 'Trial Board Development',
            'created_by' => $boardCreator->id,
        ]);

        $board->members()->attach([
            $boardCreator->id,
            $boardMember->id
        ]);
    }
}
