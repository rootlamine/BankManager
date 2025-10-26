<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Utilisateur;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Utilisateur::class;

    public function definition()
    {
        return [
            'id' => Str::uuid(),
            'prenom' => $this->faker->firstName(),
            'nom' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'numero_telephone' => $this->faker->numerify('77#######'),
            'mot_de_passe' => bcrypt('password'), // Mot de passe par défaut
            'adresse' => $this->faker->address(),
            'role' => 'client',
            'niveau_admin' => null,
            'date_inscription' => now(),
        ];
    }

    // State pour créer un admin
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'niveau_admin' => $this->faker->randomElement(['super_admin', 'admin', 'moderateur']),
            ];
        });
    }

    // State pour créer un client
    public function client()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'client',
                'niveau_admin' => null,
            ];
        });
    }
}
