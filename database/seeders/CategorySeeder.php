<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Électronique & High-Tech'],
            ['name' => 'Clés & Badges'],
            ['name' => 'Vêtements & Accessoires'],
            ['name' => 'Documents & Cartes'],
            ['name' => 'Bijoux & Montres'],
            ['name' => 'Argent & Portefeuilles'],
            ['name' => 'Livres & Papeterie'],
            ['name' => 'Santé & Hygiène'],
            ['name' => 'Divers'],
        ];

        $now = now();

        $data = array_map(function ($category) use ($now) {
            return [
                'name' => $category['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $categories);

        DB::table('categories')->insert($data);
    }
}