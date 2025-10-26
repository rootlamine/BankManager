<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use Illuminate\Support\Str;

class UtilisateurSeeder extends Seeder
{
    public function run()
    {
        // Créer un admin par défaut
        Utilisateur::create([
            'id' => Str::uuid(),
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'email' => 'admin@bankmanager.com',
            'numero_telephone' => '771234567',
            'mot_de_passe' => bcrypt('admin123'),
            'adresse' => 'Dakar, Sénégal',
            'role' => 'admin',
            'niveau_admin' => 'super_admin',
            'date_inscription' => now(),
        ]);

        // Créer quelques clients de test
        Utilisateur::create([
            'id' => Str::uuid(),
            'prenom' => 'Amadou',
            'nom' => 'Diallo',
            'email' => 'amadou.diallo@example.com',
            'numero_telephone' => '775551234',
            'mot_de_passe' => bcrypt('password'),
            'adresse' => 'Plateau, Dakar',
            'role' => 'client',
            'date_inscription' => now(),
        ]);

        Utilisateur::create([
            'id' => Str::uuid(),
            'prenom' => 'Fatou',
            'nom' => 'Ndiaye',
            'email' => 'fatou.ndiaye@example.com',
            'numero_telephone' => '775555678',
            'mot_de_passe' => bcrypt('password'),
            'adresse' => 'Almadies, Dakar',
            'role' => 'client',
            'date_inscription' => now(),
        ]);

        Utilisateur::create([
            'id' => Str::uuid(),
            'prenom' => 'Moussa',
            'nom' => 'Sow',
            'email' => 'moussa.sow@example.com',
            'numero_telephone' => '775559012',
            'mot_de_passe' => bcrypt('password'),
            'adresse' => 'Sacré-Coeur, Dakar',
            'role' => 'client',
            'date_inscription' => now(),
        ]);

        Utilisateur::create([
            'id' => Str::uuid(),
            'prenom' => 'Aissatou',
            'nom' => 'Diop',
            'email' => 'aissatou.diop@example.com',
            'numero_telephone' => '775553456',
            'mot_de_passe' => bcrypt('password'),
            'adresse' => 'Ouakam, Dakar',
            'role' => 'client',
            'date_inscription' => now(),
        ]);

        // Créer 15 clients aléatoires supplémentaires
        Utilisateur::factory()->client()->count(15)->create();

        // Créer 2 admins supplémentaires
        Utilisateur::factory()->admin()->count(2)->create();
    }
}
