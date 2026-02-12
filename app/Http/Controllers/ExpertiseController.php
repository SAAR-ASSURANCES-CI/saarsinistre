<?php

namespace App\Http\Controllers;

use App\Models\Expertise;
use App\Models\Sinistre;
use App\Models\User;
use App\Http\Requests\StoreExpertiseRequest;
use App\Services\ExpertisePdfService;
use Illuminate\Support\Facades\Auth;

class ExpertiseController extends Controller
{
    public function show(Sinistre $sinistre)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }

        $expertise = $sinistre->expertise;

        return response()->json([
            'success' => true,
            'expertise' => $expertise,
            'expert' => [
                'nom_complet' => $user->nom_complet,
                'email' => $user->email,
                'telephone' => config('expertise.default_phone', $user->phone),
            ],
        ]);
    }

    private function getExpertiseOrFail(Sinistre $sinistre)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Utilisateur non authentifié.');
        }

        $expertise = $sinistre->expertise;

        if (!$expertise) {
            abort(404, 'Aucune expertise trouvée pour ce sinistre.');
        }

        return $expertise;
    }

    public function store(StoreExpertiseRequest $request, Sinistre $sinistre)
    {
        $validated = $request->validated();

        $operations = collect($validated['operations'] ?? [])
            ->map(function (array $operation) {
                return [
                    'libelle' => trim($operation['libelle'] ?? ''),
                    'echange' => filter_var($operation['echange'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'reparation' => filter_var($operation['reparation'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'controle' => filter_var($operation['controle'] ?? false, FILTER_VALIDATE_BOOLEAN),
                    'peinture' => filter_var($operation['peinture'] ?? false, FILTER_VALIDATE_BOOLEAN),
                ];
            })
            ->filter(function (array $operation) {
                return $operation['libelle'] !== '';
            })
            ->values()
            ->all();

        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }

        $data = [
            'expert_id' => $user->id,
            'date_expertise' => now()->toDateString(),
            'client_nom' => $sinistre->nom_assure ?? '',
            'collaborateur_nom' => $user->nom_complet,
            'collaborateur_telephone' => config('expertise.default_phone') ?? $user->phone,
            'collaborateur_email' => $user->email,
            'lieu_expertise' => $validated['lieu_expertise'],
            'contact_client' => $validated['contact_client'] ?? $sinistre->telephone_assure ?? '',
            'vehicule_expertise' => $validated['vehicule_expertise'] ?? '',
            'operations' => $operations,
        ];

        $expertise = Expertise::updateOrCreate(
            ['sinistre_id' => $sinistre->id],
            $data
        );

        return response()->json([
            'success' => true,
            'expertise' => $expertise,
        ]);
    }

    public function preview(Sinistre $sinistre)
    {
        $expertise = $this->getExpertiseOrFail($sinistre);
        $pdfService = new ExpertisePdfService();
        return $pdfService->previewExpertisePdf($expertise);
    }

    public function downloadPdf(Sinistre $sinistre)
    {
        $expertise = $this->getExpertiseOrFail($sinistre);
        $pdfService = new ExpertisePdfService();
        return $pdfService->downloadExpertisePdf($expertise);
    }
}
