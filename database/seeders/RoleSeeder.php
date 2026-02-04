<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $roles = [
            [
                "name" => "Administrator",
                "permissions" => ["view-user", "create-user", "edit-user", "delete-user", "view-general-department", "create-general-department", "edit-general-department", "delete-general-department", "view-department", "create-department", "edit-department", "delete-department", "view-personel", "create-personel", "edit-personel", "delete-personel", "view-document", "create-document", "edit-document", "delete-document"]
            ],

            [
                "name" => "Co-Administrator",
                "permissions" => ["view-general-department", "create-general-department", "edit-general-department", "delete-general-department", "view-department", "create-department", "edit-department", "delete-department", "view-personel", "create-personel", "edit-personel", "delete-personel", "view-document", "create-document", "edit-document", "delete-document"]
            ],
            [
                "name" => "User",
                "permissions" => ["view-document", "create-document", "edit-document", "delete-document"]
            ]
        ];
        \App\Models\Role::factory()->createMany($roles);
    }
}
