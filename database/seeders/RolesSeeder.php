<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $staff = Role::create(['name' => 'staff']);

        $manage_goods_permission = Permission::create(['name' => 'manage goods']);
        $manage_category_permission = Permission::create(['name' => 'manage category']);
        $manage_incoming_goods_permission = Permission::create(['name' => 'manage incoming goods']);
        $manage_outgoing_goods_permission = Permission::create(['name' => 'manage outgoing goods']);
        $manage_user_permission = Permission::create(['name' => 'manage user']);

        $admin->givePermissionTo([$manage_goods_permission, $manage_category_permission, 
        $manage_incoming_goods_permission, $manage_outgoing_goods_permission, $manage_user_permission]);

        $staff->givePermissionTo([$manage_goods_permission, $manage_category_permission, 
        $manage_incoming_goods_permission, $manage_outgoing_goods_permission]);
    }
}
