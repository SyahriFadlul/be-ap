<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            ['name' => 'PT. Combi Putra', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'PT. Nara Artha', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kudamas', 'created_at' => now(), 'updated_at' => now()],
        ];

        Supplier::insert($suppliers);
    }
}
