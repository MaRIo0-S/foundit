<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::where('email', 'admin@foundit.com')->value('id');

        $entries = [
            [
                'action' => 'claim.approved',
                'description' => 'Réclamation approuvée — Veste de sport Adidas (Thomas Dupont)',
                'created_at' => '2026-06-08 09:30:00',
            ],
            [
                'action' => 'claim.rejected',
                'description' => 'Réclamation rejetée — Trousseau de clés Peugeot (Marie Leroy)',
                'created_at' => '2026-06-06 14:00:00',
            ],
            [
                'action' => 'claim.approved',
                'description' => 'Réclamation approuvée — Écouteurs AirPods Pro (Sophie Martin)',
                'created_at' => '2026-06-02 10:15:00',
            ],
            [
                'action' => 'item.returned',
                'description' => 'Restitution confirmée — Écouteurs AirPods Pro',
                'created_at' => '2026-06-03 15:00:00',
            ],
            [
                'action' => 'claim.approved',
                'description' => 'Réclamation approuvée — Montre connectée Fitbit (Sophie Martin)',
                'created_at' => '2026-05-22 11:00:00',
            ],
            [
                'action' => 'item.returned',
                'description' => 'Restitution confirmée — Montre connectée Fitbit',
                'created_at' => '2026-05-24 12:30:00',
            ],
            [
                'action' => 'claim.approved',
                'description' => 'Réclamation approuvée — Montre Casio classique (Élodie Bernard)',
                'created_at' => '2026-05-18 16:45:00',
            ],
            [
                'action' => 'item.returned',
                'description' => 'Restitution confirmée — Montre Casio classique',
                'created_at' => '2026-05-20 09:00:00',
            ],
        ];

        foreach ($entries as $entry) {
            AuditLog::create([
                'user_id' => $adminId,
                'action' => $entry['action'],
                'description' => $entry['description'],
                'created_at' => $entry['created_at'],
                'updated_at' => $entry['created_at'],
            ]);
        }
    }
}
