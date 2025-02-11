<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Sepatu Wanita Kasual',
                'slug' => 'sepatu-wanita-kasual',
                'icon' => 'icon-casual.png',
                'gender' => 'wanita',
                'subcategory' => 'kasual',
                'brand' => 'Adidas',
            ],
            [
                'name' => 'Sepatu Pria Formal',
                'slug' => 'sepatu-pria-formal',
                'icon' => 'icon-formal.png',
                'gender' => 'pria',
                'subcategory' => 'formal',
                'brand' => 'Nike',
            ],
        ];
    }
}
