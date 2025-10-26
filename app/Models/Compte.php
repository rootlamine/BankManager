<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Compte extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'numero_telephone',
        'mot_de_passe',
        'adresse',
        'role',
        'niveau_admin',
        'date_inscription',
    ];

    protected $hidden = [
        'mot_de_passe',
    ];

    protected $casts = [
        'date_inscription' => 'datetime',
    ];

    // Relation: Un utilisateur peut avoir plusieurs comptes
    public function comptes()
    {
        return $this->hasMany(Compte::class);
    }

    // Vérifier si l'utilisateur est admin
    public function estAdmin()
    {
        return $this->role === 'admin';
    }

    // Vérifier si l'utilisateur est client
    public function estClient()
    {
        return $this->role === 'client';
    }

    // Override pour Laravel Auth
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }
}
