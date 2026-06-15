<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClaimSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::pluck('id', 'email');
        $items = Item::pluck('id', 'name');
        $adminId = $users['admin@foundit.com'];

        $claimantPhones = [
            'sophie.martin@lycee-demo.fr' => '07 11 22 33 44',
            'thomas.dupont@lycee-demo.fr' => '07 22 33 44 55',
            'marie.leroy@lycee-demo.fr' => '07 33 44 55 66',
            'elodie.bernard@lycee-demo.fr' => '07 44 55 66 77',
            'jp.moreau@lycee-demo.fr' => '07 55 66 77 88',
        ];

        $claims = [
            [
                'item' => 'iPhone 13 Noir',
                'claimant' => 'sophie.martin@lycee-demo.fr',
                'claim_notes' => 'C’est mon iPhone : fond d’écran avec mon chien (golden retriever), code commençant par 2005, petite rayure sur le capteur photo arrière.',
                'status' => 'pending',
                'created_at' => '2026-06-09 10:15:00',
            ],
            [
                'item' => 'Veste de sport Adidas',
                'claimant' => 'thomas.dupont@lycee-demo.fr',
                'claim_notes' => 'La veste m’appartient : initiales « T.D. » sur l’étiquette intérieure au marqueur noir. Oubliée mardi après le cours de basket.',
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => '2026-06-08 09:30:00',
                'created_at' => '2026-06-07 16:20:00',
            ],
            [
                'item' => 'Carte d’identité nationale',
                'claimant' => 'thomas.dupont@lycee-demo.fr',
                'claim_notes' => 'C’est ma carte d’identité. Je m’appelle Thomas Dupont, né le 12 mars 2004 à Lyon.',
                'status' => 'pending',
                'created_at' => '2026-06-10 08:00:00',
            ],
            [
                'item' => 'Trousseau de clés Peugeot',
                'claimant' => 'marie.leroy@lycee-demo.fr',
                'claim_notes' => 'Je pensais que c’étaient mes clés, mais ma voiture est une Renault. Je me suis trompée d’annonce.',
                'status' => 'rejected',
                'reviewed_by' => $adminId,
                'reviewed_at' => '2026-06-06 14:00:00',
                'rejection_reason' => 'Le trousseau correspond à une clé Peugeot, incompatible avec le véhicule déclaré par la réclamante.',
                'created_at' => '2026-06-05 11:30:00',
            ],
            [
                'item' => 'Portefeuille en cuir marron',
                'claimant' => 'thomas.dupont@lycee-demo.fr',
                'claim_notes' => 'C’est mon portefeuille. Carte de transport scolaire avec ma photo à l’intérieur, billet de 10 € plié dans la poche secrète.',
                'status' => 'pending',
                'created_at' => '2026-06-10 09:45:00',
            ],
            [
                'item' => 'Montre connectée Fitbit',
                'claimant' => 'sophie.martin@lycee-demo.fr',
                'claim_notes' => 'Ma montre Fitbit, bracelet noir taille S. Dernier exercice enregistré : course de 5 km samedi matin.',
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => '2026-05-22 11:00:00',
                'created_at' => '2026-05-20 15:30:00',
            ],
            [
                'item' => 'Écouteurs AirPods Pro',
                'claimant' => 'sophie.martin@lycee-demo.fr',
                'claim_notes' => 'Mes AirPods : boîtier avec petite rayure sur le couvercle, numéro de série correspondant à ma facture Apple.',
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => '2026-06-02 10:15:00',
                'created_at' => '2026-05-30 12:00:00',
            ],
            [
                'item' => 'Montre Casio classique',
                'claimant' => 'elodie.bernard@lycee-demo.fr',
                'claim_notes' => 'Ma montre Casio offerte par ma mère, gravure « E.B. » au dos du boîtier.',
                'status' => 'approved',
                'reviewed_by' => $adminId,
                'reviewed_at' => '2026-05-18 16:45:00',
                'created_at' => '2026-05-16 09:00:00',
            ],
            [
                'item' => 'Badge d’accès personnel',
                'claimant' => 'jp.moreau@lycee-demo.fr',
                'claim_notes' => 'Mon badge du secrétariat, perdu lundi matin en arrivant au travail.',
                'status' => 'pending',
                'created_at' => '2026-06-10 07:30:00',
            ],
        ];

        foreach ($claims as $claim) {
            Claim::create([
                'item_id' => $items[$claim['item']],
                'user_id' => $users[$claim['claimant']],
                'claim_notes' => $claim['claim_notes'],
                'contact_phone' => $claimantPhones[$claim['claimant']] ?? '07 12 34 56 78',
                'status' => $claim['status'],
                'reviewed_by' => $claim['reviewed_by'] ?? null,
                'reviewed_at' => $claim['reviewed_at'] ?? null,
                'rejection_reason' => $claim['rejection_reason'] ?? null,
                'created_at' => $claim['created_at'],
                'updated_at' => $claim['reviewed_at'] ?? $claim['created_at'],
            ]);
        }
    }
}
