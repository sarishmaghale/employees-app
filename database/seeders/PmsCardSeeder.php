<?php

namespace Database\Seeders;

use App\Models\PmsBoard;
use App\Models\PmsCard;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PmsCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $board = PmsBoard::where('board_name', 'Trial Board Development')->first();
        PmsCard::create([
            'title' => 'To Do',
            'board_id' => $board->id
        ]);
    }
}
