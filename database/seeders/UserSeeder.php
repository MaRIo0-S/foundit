<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin FoundIt',
                'email' => 'admin@foundit.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Sophie Martin',
                'email' => 'sophie.martin@lycee-demo.fr',
                'role' => 'user',
            ],
            [
                'name' => 'Thomas Dupont',
                'email' => 'thomas.dupont@lycee-demo.fr',
                'role' => 'user',
            ],
            [
                'name' => 'Marie Leroy',
                'email' => 'marie.leroy@lycee-demo.fr',
                'role' => 'user',
            ],
            [
                'name' => 'Jean-Pierre Moreau',
                'email' => 'jp.moreau@lycee-demo.fr',
                'role' => 'user',
            ],
            [
                'name' => 'Élodie Bernard',
                'email' => 'elodie.bernard@lycee-demo.fr',
                'role' => 'user',
            ],
            [
                'name' => 'Marc Lefebvre',
                'email' => 'marc.lefebvre@lycee-demo.fr',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => $user['role'],
            ]);
        }
    }
}
