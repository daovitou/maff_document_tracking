<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        \App\Models\Admin::factory()->createMany([
            [
                'display_name' => 'Mr. DAO VITOU',
                'username' => 'vitoudao',
                'email' => 'vitoduao@gmail.com',
                'password' => bcrypt('Pa$$w0rd$123$'),
                'is_system' => true,
            ],
            [
                'display_name' => 'Mr. Sea Peng',
                'username' => 'seapeng',
                'email' => 'seapeng@gmail.com',
                'password' => bcrypt('Smart@2026$123$'),
                'is_system' => true,
            ]
        ]);
    }
}
