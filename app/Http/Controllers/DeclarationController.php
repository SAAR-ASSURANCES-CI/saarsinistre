<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DocumentSinistre;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class DeclarationController extends Controller
{

    public function create()
    {
        return view('declaration.formulaire');
    }

    public function store(Request $request): JsonResponse
    {
        try {

            $validated = $this->validateRequest($request);

            DB::beginTransaction();

            $user = $this->creerCompteAssure($validated);

            $this->envoyerSMSConnexion($user, $validated['telephone_assure']);

            $validated['assure_id'] = $user->id;
            $sinistre = $this->creerSinistre($validated);

            $this->gererDocuments($request, $sinistre);

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


    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'nom_assure' => 'required|string|max:255',
            'email_assure' => 'nullable|email|max:255',
            'telephone_assure' => 'required|string|max:20',
            'numero_police' => 'required|string|max:50',

            'date_sinistre' => 'required|date|before_or_equal:today',
            'heure_sinistre' => 'nullable|date_format:H:i',
            'lieu_sinistre' => 'required|string|max:500',
            'circonstances' => 'required|string|max:2000',
            'conducteur_nom' => 'required|string|max:255',

            'constat_autorite' => 'boolean',
            'officier_nom' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'commissariat' => 'nullable|required_if:constat_autorite,true|string|max:255',
            'dommages_releves' => 'nullable|string|max:1000',

            'carte_grise_recto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'carte_grise_verso' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'visite_technique_recto' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'visite_technique_verso' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'attestation_assurance' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',
            'permis_conduire' => 'required|file|mimes:pdf,jpg,jpeg,png|max:1280',

            'photos_vehicule' => 'nullable|array|max:100',
            'photos_vehicule.*' => 'file|mimes:jpg,jpeg,png|max:1280',
        ], [
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

    private function declencherNotifications(Sinistre $sinistre): void
    {
        try {
            //SEND EMAIL TO GESTIONNAIRES
            $this->sendEmail($sinistre);

            //SEND SMS TO ASSURE
            $this->sendSmsConfirmation($sinistre);

            // $this->declencherWorkflowN8n('nouveau_sinistre', $sinistre);
        } catch (Exception $e) {
            Log::error('Erreur lors des notifications: ' . $e->getMessage());
        }
    }

    private function genererNumeroAssure(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randdomString = '';
        $prefix = 'SAAR-';

        for ($i = 0; $i < 4; $i++) {
            $randdomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $prefix . $randdomString;
    }

    private function genererMotDePasseTemporaire(): string
    {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    private function creerCompteAssure(array $data): User
    {
        $numeroAssure = $this->genererNumeroAssure();
        $motDePasseTemporaire = $this->genererMotDePasseTemporaire();

        $user = User::create([
            'numero_assure' => $numeroAssure,
            'nom_complet' => $data['nom_assure'],
            'email' => $data['email_assure'] ?? null,
            'password' => Hash::make($motDePasseTemporaire),
            'password_temp' => $motDePasseTemporaire,
            'password_expires_at' => now()->addHours(48),
            'role' => 'assure',
        ]);

        return $user;
    }

    private function envoyerSMSConnexion(User $user, string $telephone): void
    {
        try {
            $orangeService = app(\App\Services\OrangeService::class);

            $message = "SAAR ASSURANCE\n";
            $message .= "Votre espace client est prêt:\n";
            $message .= "Identifiant: {$user->numero_assure}\n";
            $message .= "Code: {$user->password_temp}\n";
            $message .= "Valable 48h\n";

            $orangeService->sendSmsConfirmationSinistre(
                $telephone,
                $user->nom_complet,
                "{$user->numero_assure}"
            );
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de connexion: ' . $e->getMessage());
        }
    }

    private function sendEmail(Sinistre $sinistre)
    {
        try {
            $gestionnaires = User::where('role', 'gestionnaire')
                ->where('actif', true)
                ->get();

            dispatch(function () use ($sinistre, $gestionnaires) {

                $dataEmail = [
                    'sinistre' => $sinistre,
                    'url_sinistre' => route('dashboard'),
                    'company' => [
                        'name' => 'SAAR ASSURANCE',
                        'phone' => '+225 20 30 30 30',
                        'email' => 'contact@saar-assurance.ci',
                        'address' => 'Abidjan, Côte d\'Ivoire'
                    ]
                ];

                foreach ($gestionnaires as $gestionnaire) {
                    Mail::send('emails.nouveau-sinistre', $dataEmail, function ($message) use ($gestionnaire, $sinistre) {
                        $message->to($gestionnaire->email, $gestionnaire->nom_complet)
                            ->subject('Nouveau sinistre déclaré - N° ' . $sinistre->numero_sinistre)
                            ->from(config('mail.from.address'), config('mail.from.name'));
                    });
                }
            })->delay(now()->addSeconds(5));
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails aux gestionnaires: ' . $e->getMessage(), [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre
            ]);
        }
    }

    private function sendSmsConfirmation(Sinistre $sinistre): void
    {
        try {
            if (empty($sinistre->telephone_assure)) {
                Log::warning('Numéro de téléphone manquant pour le sinistre', [
                    'sinistre_id' => $sinistre->id,
                    'numero_sinistre' => $sinistre->numero_sinistre
                ]);
                return;
            }

            $orangeService = app(\App\Services\OrangeService::class);

            $orangeService->sendSmsConfirmationSinistre(
                $sinistre->telephone_assure,
                $sinistre->nom_assure,
                $sinistre->numero_sinistre
            );
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS de confirmation', [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Déclencher le workflow n8n
     */
    private function declencherWorkflowN8n(string $webhook, Sinistre $sinistre): void
    {
        $webhookUrl = config('n8n.webhook_url', default: env('N8N_WEBHOOK_URL'));

        if (!$webhookUrl) {
            Log::warning('URL webhook n8n non configurée');
            return;
        }

        try {
            $payload = [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'nom_assure' => $sinistre->nom_assure,
                'email_assure' => $sinistre->email_assure ?? '',
                'telephone_assure' => $sinistre->telephone_assure,
                'numero_police' => $sinistre->numero_police,
                'date_sinistre' => $sinistre->date_sinistre,
                'heure_sinistre' => $sinistre->heure_sinistre ? $sinistre->heure_sinistre->format('H:i') : 'Non précisée',
                'lieu_sinistre' => $sinistre->lieu_sinistre,
                'circonstances' => $sinistre->circonstances,
                'conducteur_nom' => $sinistre->conducteur_nom,
                'constat_autorite' => $sinistre->constat_autorite,
                'statut' => ucfirst(str_replace('_', ' ', $sinistre->statut)),
                'date_creation' => $sinistre->created_at->format('d/m/Y H:i'),

                'officier_nom' => $sinistre->officier_nom ?? '',
                'commissariat' => $sinistre->commissariat ?? '',
                'dommages_releves' => $sinistre->dommages_releves ?? '',
            ];

            $response = Http::timeout(10)->post($webhookUrl . '/' . $webhook, $payload);

            if ($response->successful()) {
                Log::info("Webhook n8n déclenché avec succès pour le sinistre {$sinistre->numero_sinistre}");
            } else {
                Log::warning("Échec du webhook n8n: " . $response->status() . " - " . $response->body());
            }
        } catch (Exception $e) {
            Log::error('Erreur webhook n8n: ' . $e->getMessage());
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
