<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'password' => Hash::make('admin')
        ]);
        $admin->assignRole('admin');

        $staff = User::create([
            'username' => 'staff',
            'password' => Hash::make('staff')
        ]);
        $staff->assignRole('staff');
    }
}
