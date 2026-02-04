<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $gds = [
            [
                "name"=>"អគ្គលេខាធិការដ្ឋាន"
            ],
            [
                "name"=>"អគ្គនាយកដ្ឋានក្សេត្រសាស្ត្រ"
            ],
            [
                "name"=>"អគ្គនាយកដ្ឋានសុខភាពសត្វ និងផលិតកម្មសត្វ"
            ],
            [
                "name"=>"អគ្គនាយកដ្ឋានជលផល"
            ],
            [
                "name"=>"អគ្គនាយកដ្ឋានព្រៃឈើ និងសត្វព្រៃ"
            ],
            [
                "name"=>"គ្រឹះស្ថានសាធារណៈរដ្ឋបាល"
            ],
            [
                "name"=>"អគ្គនាយកដ្ឋានសវនកម្មផ្ទៃក្នុង"
            ]
        ];
        \App\Models\Gd::factory()->createMany($gds);
    }
}
