<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Compte;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['depot', 'retrait', 'virement']);
        $compte = Compte::inRandomOrder()->first() ?? Compte::factory()->create();

        return [
            'id' => Str::uuid(),
            'type' => $type,
            'montant' => $this->faker->randomFloat(2, 1000, 500000),
            'status' => $this->faker->randomElement(['complet', 'attente', 'echec']),
            'description' => $this->faker->sentence(),
            'numero_compte_source' => $compte->numero_compte,
            'numero_compte_destination' => $type === 'virement' ? 'C' . $this->faker->numerify('########') : null,
            'compte_id' => $compte->id,
            'date_transaction' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    // State pour dépôt
    public function depot()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'depot',
                'numero_compte_destination' => null,
            ];
        });
    }

    // State pour retrait
    public function retrait()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'retrait',
                'numero_compte_destination' => null,
            ];
        });
    }

    // State pour virement
    public function virement()
    {
        return $this->state(function (array $attributes) {
            $compteDestination = Compte::where('id', '!=', $attributes['compte_id'])->inRandomOrder()->first();
            return [
                'type' => 'virement',
                'numero_compte_destination' => $compteDestination ? $compteDestination->numero_compte : 'C' . $this->faker->numerify('########'),
            ];
        });
    }

    // State pour transaction complète
    public function complet()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'complet',
            ];
        });
    }

    // State pour transaction en attente
    public function attente()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'attente',
            ];
        });
    }
}
