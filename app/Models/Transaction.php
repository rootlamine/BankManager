<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'type',
        'montant',
        'status',
        'description',
        'numero_compte_source',
        'numero_compte_destination',
        'compte_id',
        'date_transaction',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_transaction' => 'datetime',
    ];

    // Relation: Une transaction appartient à un compte
    public function compte()
    {
        return $this->belongsTo(Compte::class);
    }

    // Récupérer le compte source
    public function compteSource()
    {
        return Compte::where('numero_compte', $this->numero_compte_source)->first();
    }

    // Récupérer le compte destination (pour les virements)
    public function compteDestination()
    {
        if ($this->numero_compte_destination) {
            return Compte::where('numero_compte', $this->numero_compte_destination)->first();
        }
        return null;
    }

    // Valider la transaction
    public function valider()
    {
        $this->update(['status' => 'complet']);
    }

    // Annuler la transaction
    public function annuler()
    {
        $this->update(['status' => 'echec']);
    }

    // Vérifier si la transaction est complète
    public function estComplete()
    {
        return $this->status === 'complet';
    }

    // Vérifier si c'est un virement
    public function estVirement()
    {
        return $this->type === 'virement';
    }

    // Vérifier si c'est un dépôt
    public function estDepot()
    {
        return $this->type === 'depot';
    }

    // Vérifier si c'est un retrait
    public function estRetrait()
    {
        return $this->type === 'retrait';
    }
}
