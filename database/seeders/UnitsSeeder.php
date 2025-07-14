<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'pcs'],
            ['name' => 'botol'],
            ['name' => 'sachet'],
            ['name' => 'strip'],
            ['name' => 'tube'],
            ['name' => 'blister'],
            ['name' => 'tablet'],
            ['name' => 'boks'],
            ['name' => 'dus'],
            ['name' => 'fls'],
            ['name' => 'roll'],
        ];

        Unit::insert($units);
    }
}
