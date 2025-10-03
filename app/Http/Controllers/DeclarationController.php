<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Sinistre;
use App\Models\Tiers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
            
            // Valider les fichiers uploadés de manière asynchrone
            $this->validateAsyncUploads($request, $data);
            
            $user = null;
            
            if (!empty($data['email_assure'])) {
                $user = User::where('email', $data['email_assure'])
                           ->where('role', 'assure')
                           ->first();
            }
        
            if (!$user && !empty($data['telephone_assure'])) {
                // Chercher tous les sinistres avec ce téléphone (même sans assure_id)
                $sinistresExistant = Sinistre::where('telephone_assure', $data['telephone_assure'])
                                           ->where('nom_assure', $data['nom_assure'])
                                           ->get();
                
                if ($sinistresExistant->isNotEmpty()) {
                    // Chercher un sinistre qui a déjà un assure_id
                    $sinistreAvecAssure = $sinistresExistant->whereNotNull('assure_id')->first();
                    
                    if ($sinistreAvecAssure) {
                        // Utiliser le compte existant
                        $user = User::find($sinistreAvecAssure->assure_id);
                        
                        Log::info('Compte assuré existant trouvé via téléphone', [
                            'user_id' => $user->id,
                            'nom_assure' => $data['nom_assure'],
                            'telephone_assure' => $data['telephone_assure'],
                            'sinistres_existants' => $sinistresExistant->count()
                        ]);
                    } else {
                        // Créer un nouveau compte et associer tous les sinistres existants
                        $user = $this->accountService->createAssureAccount($data, app(OrangeService::class));
                        
                        // Mettre à jour tous les sinistres existants avec le nouvel assure_id
                        $this->associerSinistresExistant($sinistresExistant, $user->id);
                        
                        Log::info('Nouveau compte créé et sinistres existants associés', [
                            'user_id' => $user->id,
                            'nom_assure' => $data['nom_assure'],
                            'telephone_assure' => $data['telephone_assure'],
                            'sinistres_associes' => $sinistresExistant->count()
                        ]);
                    }
                }
            }

            if (!$user) {
                $user = $this->accountService->createAssureAccount($data, app(OrangeService::class));
                
                Log::info('Nouveau compte assuré créé', [
                    'user_id' => $user->id,
                    'nom_assure' => $data['nom_assure'],
                    'email_assure' => $data['email_assure'] ?? 'non fourni',
                    'telephone_assure' => $data['telephone_assure'] ?? 'non fourni'
                ]);
            } else {
                Log::info('Compte assuré existant utilisé', [
                    'user_id' => $user->id,
                    'nom_assure' => $data['nom_assure'],
                    'email_assure' => $data['email_assure'] ?? 'non fourni',
                    'telephone_assure' => $data['telephone_assure'] ?? 'non fourni'
                ]);
            }
            
            $sinistre = $this->createSinistre($data + ['assure_id' => $user->id]);
            $sinistre->refresh();

            $this->handleUploadedFiles($request, $sinistre);

            if (!empty($data['implique_tiers']) && !empty($data['tiers'])) {
                $this->handleTiers($request, $sinistre, $data['tiers']);
            }
            
            $this->notificationService->triggerSinistreNotifications($sinistre);

            $this->cleanupTempFiles($request);

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

    /**
     * Traiter les fichiers uploadés (progressif ou classique)
     */
    protected function handleUploadedFiles($request, Sinistre $sinistre): void
    {

        if ($request->has('uploaded_files')) {
            $this->handleProgressiveUpload($request, $sinistre);
        } else {

            $this->documentService->handleDocuments($request, $sinistre);
        }
    }

    /**
     * Traiter les fichiers uploadés progressivement
     */
    protected function handleProgressiveUpload($request, Sinistre $sinistre): void
    {
        $uploadedFiles = json_decode($request->input('uploaded_files'), true);
        
        if (!$uploadedFiles) {
            return;
        }

        foreach ($uploadedFiles as $fileInfo) {
            if (!isset($fileInfo['stored_path']) || !isset($fileInfo['type'])) {
                Log::warning("Données de fichier incomplètes", $fileInfo);
                continue;
            }

            $tempPath = $fileInfo['stored_path'];
            
            if (!Storage::disk('public')->exists($tempPath)) {
                Log::warning("Fichier temporaire introuvable: {$tempPath}");
                continue;
            }

            // Déplacer le fichier vers son emplacement final
            $finalPath = $this->moveToFinalLocation($tempPath, $sinistre, $fileInfo['type']);
            
            if ($finalPath) {
                // Créer l'enregistrement dans la base de données
                $this->createDocumentRecord($sinistre, $fileInfo, $finalPath);
                
                Log::info("Fichier traité avec succès", [
                    'sinistre_id' => $sinistre->id,
                    'type' => $fileInfo['type'],
                    'final_path' => $finalPath
                ]);
            }
        }
    }

    /**
     * Déplacer un fichier temporaire vers son emplacement final
     */
    protected function moveToFinalLocation(string $tempPath, Sinistre $sinistre, string $type): ?string
    {
        try {
            $filename = basename($tempPath);
            $finalDirectory = "sinistres/{$sinistre->id}";
            $finalPath = "{$finalDirectory}/{$filename}";

            
            if (!Storage::disk('public')->exists($finalDirectory)) {
                Storage::disk('public')->makeDirectory($finalDirectory);
            }

            
            if (Storage::disk('public')->move($tempPath, $finalPath)) {
                return $finalPath;
            }
        } catch (Exception $e) {
            Log::error("Erreur déplacement fichier {$tempPath}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Create the document record in the database
     */
    protected function createDocumentRecord(Sinistre $sinistre, array $fileInfo, string $finalPath): void
    {
        try {
            $sinistre->documents()->create([
                'type_document' => $fileInfo['type'],
                'libelle_document' => $this->getDocumentLabel($fileInfo['type']),
                'nom_fichier' => $fileInfo['original_name'] ?? basename($finalPath),
                'nom_fichier_stocke' => basename($finalPath),
                'chemin_fichier' => $finalPath,
                'type_mime' => $fileInfo['mime_type'] ?? 'application/octet-stream',
                'taille_fichier' => $fileInfo['size'] ?? 0,
            ]);
            
            Log::info("Document créé en base de données", [
                'sinistre_id' => $sinistre->id,
                'type_document' => $fileInfo['type'],
                'chemin_fichier' => $finalPath
            ]);
        } catch (Exception $e) {
            Log::error("Erreur création document pour {$fileInfo['type']}: " . $e->getMessage());
        }
    }

    /**
     * Get the label of a document type
     */
    protected function getDocumentLabel(string $type): string
    {
        $labels = [
            'carte_grise_recto' => 'Carte grise (Recto)',
            'carte_grise_verso' => 'Carte grise (Verso)',
            'visite_technique_recto' => 'Visite technique (Recto)',
            'visite_technique_verso' => 'Visite technique (Verso)',
            'attestation_assurance' => 'Attestation d\'assurance',
            'permis_conduire' => 'Permis de conduire',
            'photo_vehicule' => 'Photo véhicule',
            'tiers_photo' => 'Photo tiers',
            'tiers_attestation' => 'Attestation tiers'
        ];

        return $labels[$type] ?? 'Document';
    }

    /**
     * Valider les fichiers uploadés de manière asynchrone
     */
    protected function validateAsyncUploads($request, $data): void
    {
        if (!$request->has('uploaded_files')) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['uploaded_files' => ['Les fichiers doivent être uploadés']]
            );
        }

        $uploadedFiles = json_decode($request->input('uploaded_files'), true);
        
        if (!$uploadedFiles || !is_array($uploadedFiles)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                ['uploaded_files' => ['Données de fichiers uploadés invalides']]
            );
        }

        // Créer un mapping des champs requis
        $requiredFields = [
            'carte_grise_recto' => 'La carte grise (recto)',
            'carte_grise_verso' => 'La carte grise (verso)',
            'visite_technique_recto' => 'La visite technique (recto)',
            'visite_technique_verso' => 'La visite technique (verso)',
            'attestation_assurance' => 'L\'attestation d\'assurance',
            'permis_conduire' => 'Le permis de conduire'
        ];

        $errors = [];

        // Vérifier que tous les champs requis sont présents
        foreach ($requiredFields as $field => $label) {
            $found = false;
            foreach ($uploadedFiles as $fileData) {
                if (isset($fileData['field']) && $fileData['field'] === $field) {
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                $errors[$field] = [$label . ' est obligatoire'];
            }
        }

        if (!empty($errors)) {
            throw new \Illuminate\Validation\ValidationException(
                validator([], []),
                $errors
            );
        }
    }

    /**
     * Associer des sinistres existants à un compte assuré
     */
    protected function associerSinistresExistant($sinistres, int $assureId): void
    {
        foreach ($sinistres as $sinistre) {
            if (is_null($sinistre->assure_id)) {
                $sinistre->update(['assure_id' => $assureId]);
                
                Log::info('Sinistre associé au compte assuré', [
                    'sinistre_id' => $sinistre->id,
                    'numero_sinistre' => $sinistre->numero_sinistre,
                    'assure_id' => $assureId
                ]);
            }
        }
    }

    /**
     * Nettoyer les fichiers temporaires après traitement
     */
    protected function cleanupTempFiles($request): void
    {
        try {
            $sessionId = $request->input('session_id');
            
            if ($sessionId) {
                $tempPath = "temp_uploads/{$sessionId}";
                
                if (Storage::disk('public')->exists($tempPath)) {
                    Storage::disk('public')->deleteDirectory($tempPath);
                    Log::info("Fichiers temporaires nettoyés pour session: {$sessionId}");
                }
            }
        } catch (Exception $e) {
            Log::warning("Erreur nettoyage fichiers temporaires: " . $e->getMessage());
        }
    }
}
