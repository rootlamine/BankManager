<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Compte;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run()
    {
        $comptes = Compte::where('status', 'actif')->get();

        foreach ($comptes as $compte) {
            // Créer 5-15 transactions par compte
            $nombreTransactions = rand(5, 15);

            for ($i = 0; $i < $nombreTransactions; $i++) {
                $type = ['depot', 'retrait', 'virement'][rand(0, 2)];
                $status = rand(1, 100) > 10 ? 'complet' : (rand(1, 2) === 1 ? 'attente' : 'echec');

                $compteDestination = null;
                if ($type === 'virement') {
                    $autresComptes = Compte::where('id', '!=', $compte->id)
                        ->where('status', 'actif')
                        ->inRandomOrder()
                        ->first();
                    $compteDestination = $autresComptes ? $autresComptes->numero_compte : null;
                }

                Transaction::create([
                    'id' => Str::uuid(),
                    'type' => $type,
                    'montant' => rand(5000, 500000),
                    'status' => $status,
                    'description' => $this->getDescription($type),
                    'numero_compte_source' => $compte->numero_compte,
                    'numero_compte_destination' => $compteDestination,
                    'compte_id' => $compte->id,
                    'date_transaction' => now()->subDays(rand(1, 180)),
                ]);
            }
        }

        // Ajouter quelques transactions spécifiques pour les comptes de test
        $amadouCompte = Compte::where('numero_compte', 'C00123456')->first();
        if ($amadouCompte) {
            Transaction::create([
                'id' => Str::uuid(),
                'type' => 'depot',
                'montant' => 250000,
                'status' => 'complet',
                'description' => 'Dépôt initial',
                'numero_compte_source' => $amadouCompte->numero_compte,
                'numero_compte_destination' => null,
                'compte_id' => $amadouCompte->id,
                'date_transaction' => now()->subDays(30),
            ]);
        }
    }

    private function getDescription($type)
    {
        $descriptions = [
            'depot' => ['Dépôt en espèces', 'Versement salaire', 'Dépôt chèque', 'Alimentation compte'],
            'retrait' => ['Retrait DAB', 'Retrait guichet', 'Retrait espèces', 'Paiement facture'],
            'virement' => ['Virement bancaire', 'Transfert compte à compte', 'Paiement fournisseur', 'Envoi argent'],
        ];

        return $descriptions[$type][array_rand($descriptions[$type])];
    }
}
