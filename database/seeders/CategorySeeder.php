<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Living Room', 'slug' => 'living-room'],
            ['name' => 'Bedroom', 'slug' => 'bedroom'],
            ['name' => 'Dining', 'slug' => 'dining'],
            ['name' => 'Office', 'slug' => 'office'],
            ['name' => 'Kitchen', 'slug' => 'kitchen'],
            ['name' => 'Outdoor', 'slug' => 'outdoor'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
