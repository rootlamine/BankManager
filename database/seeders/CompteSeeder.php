<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Compte;
use App\Models\Utilisateur;
use Illuminate\Support\Str;

class CompteSeeder extends Seeder
{
    public function run()
    {
        // Récupérer les clients créés
        $amadou = Utilisateur::where('email', 'amadou.diallo@example.com')->first();
        $fatou = Utilisateur::where('email', 'fatou.ndiaye@example.com')->first();
        $moussa = Utilisateur::where('email', 'moussa.sow@example.com')->first();
        $aissatou = Utilisateur::where('email', 'aissatou.diop@example.com')->first();

        // Créer des comptes pour Amadou Diallo
        if ($amadou) {
            Compte::create([
                'id' => Str::uuid(),
                'numero_compte' => 'C00123456',
                'titulaire' => 'Amadou Diallo',
                'type' => 'epargne',
                'status' => 'actif',
                'utilisateur_id' => $amadou->id,
            ]);
        }

        // Créer des comptes pour Fatou Ndiaye
        if ($fatou) {
            Compte::create([
                'id' => Str::uuid(),
                'numero_compte' => 'C00456789',
                'titulaire' => 'Fatou Ndiaye',
                'type' => 'cheque',
                'status' => 'actif',
                'utilisateur_id' => $fatou->id,
            ]);
        }

        // Créer des comptes pour Moussa Sow
        if ($moussa) {
            Compte::create([
                'id' => Str::uuid(),
                'numero_compte' => 'C00789123',
                'titulaire' => 'Moussa Sow',
                'type' => 'epargne',
                'status' => 'actif',
                'utilisateur_id' => $moussa->id,
            ]);
        }

        // Créer des comptes pour Aissatou Diop
        if ($aissatou) {
            Compte::create([
                'id' => Str::uuid(),
                'numero_compte' => 'C00101112',
                'titulaire' => 'Aissatou Diop',
                'type' => 'cheque',
                'status' => 'actif',
                'utilisateur_id' => $aissatou->id,
            ]);
        }

        // Créer des comptes pour les autres utilisateurs
        $autresClients = Utilisateur::where('role', 'client')
            ->whereNotIn('email', [
                'amadou.diallo@example.com',
                'fatou.ndiaye@example.com',
                'moussa.sow@example.com',
                'aissatou.diop@example.com'
            ])
            ->get();

        foreach ($autresClients as $client) {
            // Chaque client a 1 à 3 comptes
            $nombreComptes = rand(1, 3);

            for ($i = 0; $i < $nombreComptes; $i++) {
                Compte::create([
                    'id' => Str::uuid(),
                    'numero_compte' => 'C' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'titulaire' => $client->prenom . ' ' . $client->nom,
                    'type' => $i === 0 ? 'epargne' : 'cheque',
                    'status' => rand(1, 100) > 10 ? 'actif' : (rand(1, 2) === 1 ? 'bloque' : 'suspendu'),
                    'utilisateur_id' => $client->id,
                ]);
            }
        }

        // Créer quelques comptes archivés
        Compte::factory()->archive()->count(3)->create();
    }
}
