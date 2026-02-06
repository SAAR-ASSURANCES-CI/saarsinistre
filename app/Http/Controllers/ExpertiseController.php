<?php

namespace App\Http\Controllers;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Http\Requests\StoreExpertiseRequest;
use App\Services\ExpertisePdfService;

class ExpertiseController extends Controller
{
    /**
     * Récupérer l'expertise existante pour un sinistre (ou les valeurs par défaut).
     */
    public function show(Sinistre $sinistre)
    {
        $expertise = $sinistre->expertise()
            ->where('expert_id', auth()->id())
            ->first();

        $user = auth()->user();

        return response()->json([
            'success' => true,
            'expertise' => $expertise,
            'expert' => [
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * Créer ou mettre à jour l'expertise pour un sinistre.
     */
    public function store(StoreExpertiseRequest $request, Sinistre $sinistre)
    {
        $validated = $request->validated();

        // Nettoyer les opérations : trim libellé et supprimer les lignes vides
        $operations = collect($validated['operations'] ?? [])
            ->map(function (array $operation) {
                return [
                    'libelle' => trim($operation['libelle'] ?? ''),
                    'echange' => !empty($operation['echange']),
                    'reparation' => !empty($operation['reparation']),
                    'controle' => !empty($operation['controle']),
                    'peinture' => !empty($operation['peinture']),
                ];
            })
            ->filter(function (array $operation) {
                return $operation['libelle'] !== '';
            })
            ->values()
            ->all();

        $user = $request->user();

        // On reconstruit les champs pré-remplis côté backend pour garantir l'intégrité.
        $data = [
            'date_expertise' => now()->toDateString(),
            'client_nom' => $sinistre->nom_assure ?? '',
            'collaborateur_nom' => $user->nom_complet,
            'collaborateur_telephone' => '0747707127/0711236714',
            'collaborateur_email' => $user->email,
            'lieu_expertise' => $validated['lieu_expertise'],
            'contact_client' => $validated['contact_client'] ?? $sinistre->telephone_assure ?? '',
            'vehicule_expertise' => $validated['vehicule_expertise'] ?? '',
            'operations' => $operations,
        ];

        $expertise = Expertise::updateOrCreate(
            [
                'sinistre_id' => $sinistre->id,
                'expert_id' => auth()->id(),
            ],
            $data
        );

        return response()->json([
            'success' => true,
            'expertise' => $expertise,
        ]);
    }

    /**
     * Prévisualiser le PDF de l'expertise.
     */
    public function preview(Sinistre $sinistre)
    {
        $expertise = $sinistre->expertise()
            ->where('expert_id', auth()->id())
            ->first();

        if (!$expertise) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune expertise trouvée pour ce sinistre.',
            ], 404);
        }

        $pdfService = new ExpertisePdfService();
        return $pdfService->previewExpertisePdf($expertise);
    }

    /**
     * Télécharger le PDF de l'expertise.
     */
    public function downloadPdf(Sinistre $sinistre)
    {
        $expertise = $sinistre->expertise()
            ->where('expert_id', auth()->id())
            ->first();

        if (!$expertise) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune expertise trouvée pour ce sinistre.',
            ], 404);
        }

        $pdfService = new ExpertisePdfService();
        return $pdfService->downloadExpertisePdf($expertise);
    }
}
