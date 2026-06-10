<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $claims = [
            [
                'item_id' => 1,
                'user_id' => 3,
                'claim_notes' => 'C’est mon iPhone, le fond d’écran est une photo de mon chien (un golden retriever) et le code de déverrouillage commence par 2005. Il y a aussi une petite rayure sur le capteur photo arrière.',
                'status' => 'pending',
            ],
            [
                'item_id' => 3,
                'user_id' => 4,
                'claim_notes' => 'La veste Adidas m’appartient, j’ai écrit mes initiales "T.D." sur l’étiquette intérieure au marqueur noir. Je l’ai oubliée mardi dernier après mon cours de basket.',
                'status' => 'approved',
            ],
            [
                'item_id' => 4,
                'user_id' => 5,
                'claim_notes' => 'Je pense que c’est ma carte d’identité perdue. Je m’appelle Thomas Dupont, né le 12 mars 2004.',
                'status' => 'pending',
            ],
            [
                'item_id' => 2,
                'user_id' => 6,
                'claim_notes' => 'Je réclame les clés, mais mon modèle est une Renault et non une Peugeot, j’ai dû me tromper d’annonce.',
                'status' => 'rejected',
            ],
            [
                'item_id' => 6,
                'user_id' => 7,
                'claim_notes' => 'C’est mon portefeuille en cuir marron. À l’intérieur, il y a ma carte de transport scolaire avec ma photo bien visible, ainsi qu’un billet de 10 euros plié en quatre dans la poche secrète.',
                'status' => 'pending',
            ],
        ];

        $now = now();

        $data = array_map(function ($claim) use ($now) {
            return [
                'item_id' => $claim['item_id'],
                'user_id' => $claim['user_id'],
                'claim_notes' => $claim['claim_notes'],
                'status' => $claim['status'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $claims);

        DB::table('claims')->insert($data);
    }
}