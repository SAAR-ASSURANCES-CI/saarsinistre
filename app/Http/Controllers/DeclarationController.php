<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Sinistre;
use App\Models\Tiers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Services\AssureAccountService;
use App\Services\OrangeService;
use App\Services\PdfGenerationService;
use App\Services\SinistreDocumentService;
use App\Http\Requests\StoreDeclarationRequest;

class DeclarationController extends Controller
{
    protected $accountService;
    protected $documentService;
    protected $notificationService;
    protected $pdfService;

    public function __construct(
        AssureAccountService $accountService,
        SinistreDocumentService $documentService,
        NotificationService $notificationService,
        PdfGenerationService $pdfService
    ) {
        $this->accountService = $accountService;
        $this->documentService = $documentService;
        $this->notificationService = $notificationService;
        $this->pdfService = $pdfService;
    }

    public function create()
    {
        return view('declaration.formulaire');
    }

    public function store(StoreDeclarationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            
            $user = null;
            if (!empty($data['email_assure'])) {
                $user = User::where('email', $data['email_assure'])->first();
            }
            if (!$user && !empty($data['telephone_assure'])) {
                $sinistre = Sinistre::where('telephone_assure', $data['telephone_assure'])->first();
                if ($sinistre && $sinistre->assure_id) {
                    $user = User::find($sinistre->assure_id);
                }
            }
            if (!$user) {
               
                $username = (new AssureAccountService)->generateUniqueUsername($data['nom_assure']);
                $user = User::where('username', $username)->first();
            }

            if (!$user) {
                
                $user = $this->accountService->createAssureAccount($data, app(OrangeService::class));
            }
            
            $sinistre = $this->createSinistre($data + ['assure_id' => $user->id]);
            $sinistre->refresh();

            $this->documentService->handleDocuments($request, $sinistre);

            if (!empty($data['implique_tiers']) && !empty($data['tiers'])) {
                $this->handleTiers($request, $sinistre, $data['tiers']);
            }
            
            $this->notificationService->triggerSinistreNotifications($sinistre);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Votre sinistre a été déclaré avec succès.',
                'numero_sinistre' => $sinistre->numero_sinistre,
                'redirect_url' => route('declaration.confirmation', $sinistre->id)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la création du sinistre: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la soumission. Veuillez réessayer.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    protected function createSinistre(array $validated): Sinistre
    {
        $donneesSinistre = collect($validated)->except([
            'carte_grise_recto',
            'carte_grise_verso',
            'visite_technique_recto',
            'visite_technique_verso',
            'attestation_assurance',
            'permis_conduire',
            'photos_vehicule',
            'tiers',
            'tiers_photos_1',
            'tiers_photos_2',
            'tiers_photos_3',
            'tiers_photos_4',
            'tiers_photos_5',
            'tiers_photos_6',
            'tiers_photos_7',
            'tiers_photos_8',
            'tiers_photos_9',
            'tiers_photos_10',
            'tiers_attestation_1',
            'tiers_attestation_2',
            'tiers_attestation_3',
            'tiers_attestation_4',
            'tiers_attestation_5',
            'tiers_attestation_6',
            'tiers_attestation_7',
            'tiers_attestation_8',
            'tiers_attestation_9',
            'tiers_attestation_10'
        ])->toArray();

        $donneesSinistre['constat_autorite'] = (bool)($donneesSinistre['constat_autorite'] ?? false);
        $donneesSinistre['statut'] = 'en_attente';

        return Sinistre::create($donneesSinistre);
    }

    protected function handleTiers($request, Sinistre $sinistre, array $tiersData): void
    {
        foreach ($tiersData as $numero => $tiersInfo) {
           
            $tiersInfo = array_filter($tiersInfo, function($value) {
                return !is_null($value) && $value !== '';
            });
            
           
            if (!empty($tiersInfo)) {
                $tiers = $sinistre->tiers()->create([
                    'numero_tiers' => (int)$numero,
                    'nom_conducteur' => $tiersInfo['nom_conducteur'] ?? null,
                    'prenom_conducteur' => $tiersInfo['prenom_conducteur'] ?? null,
                    'telephone' => $tiersInfo['telephone'] ?? null,
                    'email' => $tiersInfo['email'] ?? null,
                    'adresse' => $tiersInfo['adresse'] ?? null,
                    'marque_vehicule' => $tiersInfo['marque_vehicule'] ?? null,
                    'modele_vehicule' => $tiersInfo['modele_vehicule'] ?? null,
                    'immatriculation' => $tiersInfo['immatriculation'] ?? null,
                    'compagnie_assurance' => $tiersInfo['compagnie_assurance'] ?? null,
                    'numero_police_assurance' => $tiersInfo['numero_police_assurance'] ?? null,
                    'details_supplementaires' => $tiersInfo['details_supplementaires'] ?? null,
                ]);

                $this->handleTiersDocuments($request, $tiers, $numero);
            }
        }
    }

    protected function handleTiersDocuments($request, $tiers, $numero): void
    {
       
        if ($request->hasFile("tiers_photos_{$numero}")) {
            $photos = $request->file("tiers_photos_{$numero}");
            foreach ($photos as $index => $photo) {
                if ($photo->isValid()) {
                    $filename = 'tiers_' . $tiers->id . '_photo_' . ($index + 1) . '_' . time() . '.' . $photo->getClientOriginalExtension();
                    $path = $photo->storeAs('tiers/photos', $filename, 'public');
                    
                    $tiers->documents()->create([
                        'type_document' => 'photo_vehicule',
                        'nom_fichier' => $photo->getClientOriginalName(),
                        'chemin_fichier' => $path,
                        'taille_fichier' => $photo->getSize(),
                        'extension' => $photo->getClientOriginalExtension(),
                    ]);
                }
            }
        }

        if ($request->hasFile("tiers_attestation_{$numero}")) {
            $attestation = $request->file("tiers_attestation_{$numero}");
            if ($attestation->isValid()) {
                $filename = 'tiers_' . $tiers->id . '_attestation_' . time() . '.' . $attestation->getClientOriginalExtension();
                $path = $attestation->storeAs('tiers/attestations', $filename, 'public');
                
                $tiers->documents()->create([
                    'type_document' => 'attestation_assurance',
                    'nom_fichier' => $attestation->getClientOriginalName(),
                    'chemin_fichier' => $path,
                    'taille_fichier' => $attestation->getSize(),
                    'extension' => $attestation->getClientOriginalExtension(),
                ]);
            }
        }
    }

    public function downloadRecu($sinistreId)
    {
        try {
            return $this->pdfService->generateSinistreReceipt($sinistreId);
        } catch (Exception $e) {
            Log::error('Erreur lors du téléchargement du reçu: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Impossible de générer le reçu. Veuillez réessayer.');
        }
    }

    public function confirmation($sinistreId)
    {
        $sinistre = Sinistre::with(['documents', 'tiers.documents'])->findOrFail($sinistreId);
        return view('declaration.confirmation', compact('sinistre'));
    }

    public function statut($numeroSinistre): JsonResponse
    {
        $sinistre = Sinistre::where('numero_sinistre', $numeroSinistre)->first();

        if (!$sinistre) {
            return response()->json([
                'success' => false,
                'message' => 'Sinistre non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'sinistre' => [
                'numero_sinistre' => $sinistre->numero_sinistre,
                'statut' => $sinistre->statut,
                'date_sinistre' => $sinistre->date_sinistre,
                'assure' => $sinistre->nom_assure,
                'gestionnaire' => $sinistre->gestionnaire->nom_complet ?? 'Non assigné',
                'derniere_maj' => $sinistre->updated_at->format('d/m/Y H:i')
            ]
        ]);
    }
}
