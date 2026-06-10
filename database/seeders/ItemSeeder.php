<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'category_id' => 1,
                'location_id' => 1,
                'name' => 'iPhone 13 Noir',
                'description' => 'Trouvé sur une chaise de la réception avec une coque transparente et un écran fissuré dans le coin supérieur gauche.',
                'found_date' => '2026-06-01',
                'image_path' => 'items/iphone13.jpg',
                'status' => 'available',
                'user_id' => 1
            ],
            [
                'category_id' => 2,
                'location_id' => 6,
                'name' => 'Trousseau de clés de voiture',
                'description' => 'Un trousseau comprenant une clé de marque Peugeot, une clé de maison classique et un porte-clé jeton de caddie.',
                'found_date' => '2026-06-02',
                'image_path' => null,
                'status' => 'available',
                'user_id' => 2

            ],
            [
                'category_id' => 3,
                'location_id' => 5,
                'name' => 'Veste de sport Adidas',
                'description' => 'Veste de survêtement bleue de taille M, oubliée sur les bancs à côté des vestiaires du gymnase.',
                'found_date' => '2026-05-28',
                'image_path' => 'items/veste_adidas.jpg',
                'status' => 'claimed',
                'user_id' => 1
            ],
            [
                'category_id' => 4,
                'location_id' => 4,
                'name' => 'Carte d’identité nationale',
                'description' => 'Carte trouvée glissée entre deux étagères de la section des romans. Au nom de Dupont Thomas.',
                'found_date' => '2026-06-04',
                'image_path' => null,
                'status' => 'available',
                'user_id' => 2
            ],
            [
                'category_id' => 5,
                'location_id' => 9,
                'name' => 'Montre connectée Fitbit',
                'description' => 'Montre avec bracelet en caoutchouc noir, trouvée près des lavabos. La batterie est complètement déchargée.',
                'found_date' => '2026-05-20',
                'image_path' => 'items/fitbit.jpg',
                'status' => 'returned',
                'user_id' => 2
            ],
            [
                'category_id' => 6,
                'location_id' => 3,
                'name' => 'Portefeuille en cuir marron',
                'description' => 'Contient une carte de bus scolaire et une petite somme d’argent en espèces. Trouvé sous une table de la cantine.',
                'found_date' => '2026-06-05',
                'image_path' => null,
                'status' => 'available',
                'user_id' => 1

            ],
            [
                'category_id' => 7,
                'location_id' => 1,
                'name' => 'Manuel de Mathématiques Terminale',
                'description' => 'Livre scolaire d’algèbre laissé sur une table au fond de la salle 104.',
                'found_date' => '2026-06-02',
                'image_path' => null,
                'status' => 'available',
                'user_id' => 2

            ],
            [
                'category_id' => 1,
                'location_id' => 4,
                'name' => 'Écouteurs AirPods Pro',
                'description' => 'Boîtier de charge contenant les deux écouteurs, retrouvé sans inscription particulière sur une table de travail.',
                'found_date' => '2026-05-25',
                'image_path' => 'items/airpods.jpg',
                'status' => 'returned',
                'user_id' => 1
            ],
        ];

        $now = now();

        $data = array_map(function ($item) use ($now) {
            return [
                'category_id' => $item['category_id'],
                'location_id' => $item['location_id'],
                'name' => $item['name'],
                'description' => $item['description'],
                'found_date' => $item['found_date'],
                'image_path' => $item['image_path'],
                'status' => $item['status'],
                'user_id' => $item['user_id'],
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }, $items);

        DB::table('items')->insert($data);
    }
}