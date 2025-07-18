<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use  App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'description' => 'Devices and gadgets'
            ],
            [
                'name' => 'Clothing',
                'description' => 'Apparel and accessories'
            ],
            [
                'name' => 'Home & Kitchen',
                'description' => 'Household items and kitchenware'
            ]
        ];

        foreach ($categories as $category) {
           Category::create($category);
        }
    }
}
