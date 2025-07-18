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
use App\Jobs\SendAccountCreationSms;
use Illuminate\Support\Facades\Hash;
use App\Jobs\SendSinistreConfirmationSms;
use App\Jobs\SendSinistreNotificationEmail;
use App\Http\Requests\StoreDeclarationRequest;

class DeclarationController extends Controller
{

    public function create()
    {
        return view('declaration.formulaire');
    }

    public function store(StoreDeclarationRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = $this->creerCompteAssure($request->validated());

            SendAccountCreationSms::dispatch($user, $request->telephone_assure);

            $sinistre = $this->creerSinistre($request->validated() + ['assure_id' => $user->id]);

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
            SendSinistreConfirmationSms::dispatch($sinistre);

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

    private function sendEmail(Sinistre $sinistre)
    {
        try {
            $gestionnaires = User::where('role', 'gestionnaire')
                ->where('actif', true)
                ->get();

            if ($gestionnaires->isEmpty()) {
                Log::warning('Aucun gestionnaire actif trouvé', [
                    'sinistre_id' => $sinistre->id,
                    'numero_sinistre' => $sinistre->numero_sinistre
                ]);
                return;
            }

            SendSinistreNotificationEmail::dispatch($sinistre, $gestionnaires)
                ->delay(now()->addSeconds(5));

            Log::info('Job d\'envoi d\'email planifié avec succès', [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'nb_gestionnaires' => $gestionnaires->count()
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi des emails aux gestionnaires: ' . $e->getMessage(), [
                'sinistre_id' => $sinistre->id,
                'numero_sinistre' => $sinistre->numero_sinistre,
                'error' => $e->getMessage()
            ]);
        }
    }

    // private function sendSmsConfirmation(Sinistre $sinistre): void
    // {
    //     try {
    //         if (empty($sinistre->telephone_assure)) {
    //             Log::warning('Numéro de téléphone manquant pour le sinistre', [
    //                 'sinistre_id' => $sinistre->id,
    //                 'numero_sinistre' => $sinistre->numero_sinistre
    //             ]);
    //             return;
    //         }

    //         $orangeService = app(\App\Services\OrangeService::class);

    //         $orangeService->sendSmsConfirmationSinistre(
    //             $sinistre->telephone_assure,
    //             $sinistre->nom_assure,
    //             $sinistre->numero_sinistre
    //         );
    //     } catch (Exception $e) {
    //         Log::error('Erreur lors de l\'envoi du SMS de confirmation', [
    //             'sinistre_id' => $sinistre->id,
    //             'numero_sinistre' => $sinistre->numero_sinistre,
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }

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
