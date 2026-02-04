<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $permissions = [
            [
                "name" => "Create User",
                "slug" => "create-user",
                "heading" => "User"
            ],
            [
                "name" => "View User",
                "slug" => "view-user",
                "heading" => "User"
            ],
            [
                "name" => "Edit User",
                "slug" => "edit-user",
                "heading" => "User"
            ],
            [
                "name" => "Delete User",
                "slug" => "delete-user",
                "heading" => "User"
            ],
        ];
        \App\Models\Permission::factory()->createMany($permissions);
    }
}
