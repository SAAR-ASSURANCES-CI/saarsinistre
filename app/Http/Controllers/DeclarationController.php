<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Sinistre;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;
use App\Services\AssureAccountService;
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
                
                $user = $this->accountService->createAssureAccount($data);
            }
            
            $sinistre = $this->createSinistre($data + ['assure_id' => $user->id]);
            $sinistre->refresh();

            $this->documentService->handleDocuments($request, $sinistre);
            
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
            'photos_vehicule'
        ])->toArray();

        $donneesSinistre['constat_autorite'] = (bool)($donneesSinistre['constat_autorite'] ?? false);
        $donneesSinistre['statut'] = 'en_attente';

        return Sinistre::create($donneesSinistre);
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
        $sinistre = Sinistre::with('documents')->findOrFail($sinistreId);
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
