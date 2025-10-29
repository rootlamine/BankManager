<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCompteRequest;
use App\Http\Resources\CompteResource;
use App\Http\Resources\CompteCollection;
use App\Models\Compte;
use App\Models\Utilisateur;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class CompteController extends Controller
{
    /**
     * Lister tous les comptes (Admin) ou les comptes du client connecté
     * GET /api/v1/comptes
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Compte::query()
            ->whereIn('status', ['actif', 'bloque'])
            ->whereIn('type', ['cheque', 'epargne']);

        // Si client, ne voir que ses comptes
        if ($user->estClient()) {
            $query->where('utilisateur_id', $user->id);
        }

        // Filtres
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('statut')) {
            $query->where('status', $request->statut);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('titulaire', 'like', "%{$search}%")
                  ->orWhere('numero_compte', 'like', "%{$search}%");
            });
        }

        // Tri
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $sortMap = [
            'dateCreation' => 'created_at',
            'titulaire' => 'titulaire',
        ];

        $sortColumn = $sortMap[$sort] ?? 'created_at';
        $query->orderBy($sortColumn, $order);

        // Pagination
        $limit = min($request->get('limit', 10), 100);
        $comptes = $query->paginate($limit);

        return new CompteCollection($comptes);
    }

    /**
     * Créer un nouveau compte
     * POST /api/v1/comptes
     */
    public function store(StoreCompteRequest $request)
    {
        try {
            DB::beginTransaction();

            $clientId = $request->input('client.id');

            // Si le client n'existe pas, le créer
            if (!$clientId) {
                $password = Str::random(10);
                $code = rand(100000, 999999);

                $client = Utilisateur::create([
                    'id' => Str::uuid(),
                    'prenom' => explode(' ', $request->input('client.titulaire'))[0],
                    'nom' => explode(' ', $request->input('client.titulaire'))[1] ?? '',
                    'email' => $request->input('client.email'),
                    'numero_telephone' => $request->input('client.telephone'),
                    'mot_de_passe' => Hash::make($password),
                    'adresse' => $request->input('client.adresse'),
                    'nci' => $request->input('client.nci'),
                    'role' => 'client',
                    'code_verification' => $code,
                    'date_inscription' => now(),
                ]);

                $clientId = $client->id;

                // Envoyer email avec mot de passe
                // Mail::to($client->email)->send(new WelcomeClientMail($password));

                // Envoyer SMS avec code
                // SMS::send($client->numero_telephone, "Votre code de vérification: {$code}");

            } else {
                $client = Utilisateur::findOrFail($clientId);
            }

            // Générer un numéro de compte unique
            $numeroCompte = $this->genererNumeroCompte();

            // Créer le compte
            $compte = Compte::create([
                'id' => Str::uuid(),
                'numero_compte' => $numeroCompte,
                'titulaire' => $request->input('client.titulaire'),
                'type' => $request->type,
                'status' => 'actif',
                'utilisateur_id' => $clientId,
            ]);

            // Créer la transaction de dépôt initial
            if ($request->soldeInitial > 0) {
                Transaction::create([
                    'id' => Str::uuid(),
                    'type' => 'depot',
                    'montant' => $request->soldeInitial,
                    'status' => 'complet',
                    'description' => 'Dépôt initial à la création du compte',
                    'numero_compte_source' => $numeroCompte,
                    'numero_compte_destination' => null,
                    'compte_id' => $compte->id,
                    'date_transaction' => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Compte créé avec succès',
                'data' => new CompteResource($compte->fresh()),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'CREATION_ERROR',
                    'message' => 'Erreur lors de la création du compte',
                    'details' => $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Afficher un compte spécifique
     * GET /api/v1/comptes/{compteId}
     */
    public function show(Request $request, $compteId)
    {
        $user = $request->user();

        // Recherche locale (base de données principale)
        $compte = Compte::where('id', $compteId)
            ->whereIn('status', ['actif', 'bloque'])
            ->whereIn('type', ['cheque', 'epargne'])
            ->first();

        // Si client, vérifier qu'il possède le compte
        if ($user->estClient() && $compte && $compte->utilisateur_id !== $user->id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'Vous n\'avez pas accès à ce compte',
                ],
            ], 403);
        }

        // Si compte non trouvé en local, chercher dans ServerLess (archives)
        if (!$compte) {
            $compte = Compte::where('id', $compteId)
                ->whereIn('status', ['archive', 'suspendu'])
                ->first();

            if (!$compte) {
                return response()->json([
                    'success' => false,
                    'error' => [
                        'code' => 'COMPTE_NOT_FOUND',
                        'message' => 'Le compte avec l\'ID spécifié n\'existe pas',
                        'details' => [
                            'compteId' => $compteId,
                        ],
                    ],
                ], 404);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new CompteResource($compte),
        ]);
    }

    /**
     * Générer un numéro de compte unique
     */
    private function genererNumeroCompte()
    {
        do {
            $numero = 'C' . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Compte::where('numero_compte', $numero)->exists());

        return $numero;
    }
}
