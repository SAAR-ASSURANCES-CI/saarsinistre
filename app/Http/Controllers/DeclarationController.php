<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Sinistre;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\DocumentSinistre;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class DeclarationController extends Controller
{
    /**
     * Afficher le formulaire de déclaration
     */
    public function create()
    {
        return view('declaration.formulaire');
    }

    /**
     * Traiter la soumission du formulaire
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validation des données
            $validated = $this->validateRequest($request);

            DB::beginTransaction();

            // Créer le sinistre
            $sinistre = $this->creerSinistre($validated);

            // Gérer les documents
            $this->gererDocuments($request, $sinistre);


            // Déclencher les notifications
            $this->declencherNotifications($sinistre);

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

    /**
     * Validation des données du formulaire
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            // Informations personnelles - TOUTES REQUISES
            'nom_assure' => 'required|string|max:255',
            'email_assure' => 'nullable|email|max:255',
            'telephone_assure' => 'required|string|max:20',
            'numero_police' => 'required|string|max:50',

            // Détails du sinistre - TOUS REQUIS
            'date_sinistre' => 'required|date|before_or_equal:today',
            'heure_sinistre' => 'nullable|date_format:H:i',
            'lieu_sinistre' => 'required|string|max:500',
            'circonstances' => 'required|string|max:2000',
            'conducteur_nom' => 'required|string|max:255',

            // Constat d'autorité - OPTIONNEL
            'constat_autorite' => 'boolean',
            'officier_nom' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'commissariat' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'dommages_releves' => 'nullable|string|max:1000',

            // Documents obligatoires
            'carte_grise_recto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carte_grise_verso' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visite_technique_recto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'visite_technique_verso' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'attestation_assurance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'permis_conduire' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',

            // Photos (optionnelles)
            'photos_vehicule' => 'nullable|array|max:100',
            'photos_vehicule.*' => 'file|mimes:jpg,jpeg,png|max:1280',
        ], [
            // Messages d'erreur personnalisés en français
            'nom_assure.required' => 'Le nom complet est obligatoire.',
            'nom_assure.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email_assure.required' => 'L\'adresse email est obligatoire.',
            'email_assure.email' => 'L\'adresse email n\'est pas valide.',
            'email_assure.max' => 'L\'email ne doit pas dépasser 255 caractères.',
            'telephone_assure.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone_assure.max' => 'Le téléphone ne doit pas dépasser 20 caractères.',
            'numero_police.required' => 'Le numéro de police est obligatoire.',
            'numero_police.max' => 'Le numéro de police ne doit pas dépasser 50 caractères.',

            'date_sinistre.required' => 'La date du sinistre est obligatoire.',
            'date_sinistre.before_or_equal' => 'La date du sinistre ne peut pas être dans le futur.',
            'lieu_sinistre.required' => 'Le lieu du sinistre est obligatoire.',
            'lieu_sinistre.max' => 'Le lieu ne doit pas dépasser 500 caractères.',
            'circonstances.required' => 'La description des circonstances est obligatoire.',
            'circonstances.max' => 'La description ne doit pas dépasser 2000 caractères.',
            'conducteur_nom.required' => 'Le nom du conducteur est obligatoire.',
            'conducteur_nom.max' => 'Le nom du conducteur ne doit pas dépasser 255 caractères.',

            'officier_nom.required_if' => 'Le nom de l\'officier est requis si un constat a été établi.',
            'officier_nom.max' => 'Le nom de l\'officier ne doit pas dépasser 255 caractères.',
            'commissariat.required_if' => 'Le commissariat/brigade est requis si un constat a été établi.',
            'commissariat.max' => 'Le nom du commissariat ne doit pas dépasser 255 caractères.',
            'dommages_releves.max' => 'La description des dommages ne doit pas dépasser 1000 caractères.',

            // Messages pour les fichiers
            'carte_grise_recto.required' => 'La carte grise (recto) est obligatoire.',
            'carte_grise_verso.required' => 'La carte grise (verso) est obligatoire.',
            'visite_technique_recto.required' => 'La visite technique (recto) est obligatoire.',
            'visite_technique_verso.required' => 'La visite technique (verso) est obligatoire.',
            'attestation_assurance.required' => 'L\'attestation d\'assurance est obligatoire.',
            'permis_conduire.required' => 'Le permis de conduire est obligatoire.',
            '*.mimes' => 'Format de fichier non autorisé. Utilisez PDF, JPG, JPEG ou PNG.',
            '*.max' => 'La taille du fichier ne doit pas dépasser 5MB.',
            'photos_vehicule.max' => 'Vous ne pouvez télécharger que 100 photos maximum.',
        ]);
    }

    /**
     * Créer un nouveau sinistre
     */
    private function creerSinistre(array $validated): Sinistre
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

    /**
     * Gérer l'upload et l'enregistrement des documents
     */
    private function gererDocuments(Request $request, Sinistre $sinistre): void
    {
        $typesDocuments = [
            'carte_grise_recto' => 'Carte grise (Recto)',
            'carte_grise_verso' => 'Carte grise (Verso)',
            'visite_technique_recto' => 'Visite technique (Recto)',
            'visite_technique_verso' => 'Visite technique (Verso)',
            'attestation_assurance' => 'Attestation d\'assurance',
            'permis_conduire' => 'Permis de conduire'
        ];

        foreach ($typesDocuments as $typeInput => $libelle) {
            if ($request->hasFile($typeInput)) {
                $this->stockerDocument($request->file($typeInput), $sinistre, $typeInput, $libelle);
            }
        }

        if ($request->hasFile('photos_vehicule')) {
            foreach ($request->file('photos_vehicule') as $index => $photo) {
                $libelle = "Photo véhicule " . ($index + 1);
                $this->stockerDocument($photo, $sinistre, 'photo_vehicule', $libelle);
            }
        }
    }

    /**
     * Stocker un document individuel
     */
    private function stockerDocument($file, Sinistre $sinistre, string $type, string $libelle): void
    {
        $extension = $file->getClientOriginalExtension();
        $nomFichier = $type . '_' . time() . '_' . uniqid() . '.' . $extension;

        $chemin = $file->storeAs("sinistres/{$sinistre->id}", $nomFichier, 'public');

        DocumentSinistre::create([
            'sinistre_id' => $sinistre->id,
            'type_document' => $type,
            'libelle_document' => $libelle,
            'nom_fichier' => $file->getClientOriginalName(),
            'nom_fichier_stocke' => $nomFichier,
            'chemin_fichier' => $chemin,
            'type_mime' => $file->getMimeType(),
            'taille_fichier' => $file->getSize(),
        ]);
    }

    public function downloadRecu($sinistreId)
    {
        try {
            $sinistre = Sinistre::with('documents')->findOrFail($sinistreId);

            $data = [
                'sinistre' => $sinistre,
                'date_generation' => now()->format('d/m/Y H:i'),
                'company' => [
                    'name' => 'SAAR ASSURANCE',
                    'phone' => '+225 20 30 30 30',
                    'email' => 'contact@saar-assurance.ci',
                    'address' => 'Abidjan, Côte d\'Ivoire'
                ]
            ];

            $pdf = PDF::loadView('declaration.recu-pdf', $data);

            $pdf->setPaper('A4', 'portrait');

            $nomFichier = 'Recu_Declaration_' . $sinistre->numero_sinistre . '.pdf';

            return $pdf->download($nomFichier);
        } catch (Exception $e) {
            Log::error('Erreur lors du téléchargement du reçu: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Impossible de générer le reçu. Veuillez réessayer.');
        }
    }

    /**
     * Déclencher les notifications (n8n + email direct)
     */
    private function declencherNotifications(Sinistre $sinistre): void
    {
        try {
            $this->declencherWorkflowN8n('nouveau_sinistre', $sinistre);

            Log::info("Nouveau sinistre créé", [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'assure' => $sinistre->nom_assure,
                'email' => $sinistre->email_assure
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors des notifications: ' . $e->getMessage());
        }
    }

    /**
     * Déclencher le workflow n8n
     */
    private function declencherWorkflowN8n(string $webhook, Sinistre $sinistre): void
    {
        $webhookUrl = config('n8n.webhook_url', env('N8N_WEBHOOK_URL'));

        if (!$webhookUrl) {
            Log::warning('URL webhook n8n non configurée');
            return;
        }

        try {
            $response = Http::timeout(10)->post($webhookUrl . '/' . $webhook, [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'nom_assure' => $sinistre->nom_assure,
                'email_assure' => $sinistre->email_assure,
                'telephone_assure' => $sinistre->telephone_assure,
                'numero_police' => $sinistre->numero_police,
                'date_sinistre' => $sinistre->date_sinistre->format('Y-m-d'),
                'heure_sinistre' => $sinistre->heure_sinistre ? $sinistre->heure_sinistre->format('H:i') : null,
                'circonstances' => $sinistre->circonstances,
                'lieu_sinistre' => $sinistre->lieu_sinistre,
                'conducteur_nom' => $sinistre->conducteur_nom,
                'constat_autorite' => $sinistre->constat_autorite,
                'statut' => $sinistre->statut,
                'date_creation' => $sinistre->created_at->toISOString(),
                'url_dossier' => route('admin.sinistres.show', $sinistre->id),
            ]);

            if ($response->successful()) {
                Log::info("Webhook n8n déclenché avec succès pour le sinistre {$sinistre->numero_sinistre}");
            } else {
                Log::warning("Échec du webhook n8n: " . $response->status() . " - " . $response->body());
            }
        } catch (Exception $e) {
            Log::error('Erreur webhook n8n: ' . $e->getMessage());
        }
    }

    /**
     * Page de confirmation après soumission
     */
    public function confirmation($sinistreId)
    {
        $sinistre = Sinistre::with('documents')->findOrFail($sinistreId);

        return view('declaration.confirmation', compact('sinistre'));
    }

    /**
     * API pour vérifier le statut d'un sinistre
     */
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
                'date_sinistre' => $sinistre->date_sinistre->format('d/m/Y'),
                'assure' => $sinistre->nom_assure,
                'gestionnaire' => $sinistre->gestionnaire->name ?? 'Non assigné',
                'derniere_maj' => $sinistre->updated_at->format('d/m/Y H:i')
            ]
        ]);
    }
}
