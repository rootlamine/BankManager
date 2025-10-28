<?php

namespace Database\Factories;

use App\Models\Compte;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CompteFactory extends Factory
{
    protected $model = Compte::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'numero_compte' => 'C' . $this->faker->unique()->numerify('########'),
            'titulaire' => $this->faker->name(),
            'type' => $this->faker->randomElement(['epargne', 'cheque']),
            'status' => 'actif',
            'utilisateur_id' => Utilisateur::factory(),
        ];
    }

    // State pour compte épargne
    public function epargne()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'epargne',
            ];
        });
    }

    // State pour compte chèque
    public function cheque()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'cheque',
            ];
        });
    }

    // State pour compte bloqué
    public function bloque()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'bloque',
            ];
        });
    }

    // State pour compte archivé
    public function archive()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'archive',
            ];
        });
    }
}
