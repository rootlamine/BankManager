<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CompteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'numeroCompte' => $this->numero_compte,
            'titulaire' => $this->titulaire,
            'type' => $this->type,
            'solde' => $this->calculerSolde(),
            'devise' => 'FCFA',
            'dateCreation' => $this->created_at->toIso8601String(),
            'statut' => $this->status,
            'motifBlocage' => $this->status === 'bloque' ? $this->motif_blocage : null,
            'metadata' => [
                'derniereModification' => $this->updated_at->toIso8601String(),
                'version' => 1,
            ],
        ];
    }
}
