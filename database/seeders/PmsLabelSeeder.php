<?php

namespace Database\Seeders;

use App\Models\PmsLabel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PmsLabelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PmsLabel::firstOrCreate([
            'title' => 'Frontend',
            'color' => '#4f3977ff'
        ]);
        PmsLabel::firstOrCreate([
            'title' => 'Testing',
            'color' => '#f8770dff'
        ]);
    }
}
