<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'Salles de Classe'],
            ['name' => 'Cour de Récréation'],
            ['name' => 'Cafétéria / Cantine'],
            ['name' => 'Bibliothèque / CDI'],
            ['name' => 'Gymnase / Terrain de Sport'],
            ['name' => 'Halls & Couloirs'],
            ['name' => 'Administration / Secrétariat'],
            ['name' => 'Salle des Professeurs'],
            ['name' => 'Sanitaires Élèves'],
            ['name' => 'Laboratoires de Science'],
        ];

        $now = now();

        $data = array_map(function ($location) use ($now) {
            return [
                'name' => $location['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $locations);

        DB::table('locations')->insert($data);
    }
}