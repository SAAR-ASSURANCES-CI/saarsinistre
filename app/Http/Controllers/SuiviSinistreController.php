<?php

namespace App\Http\Controllers;

use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SuiviSinistreController extends Controller
{

    public function rechercher(Request $request)
    {
        try {
            $request->validate([
                'numero' => 'required|string|max:50',
            ], [
                'numero.required' => 'Veuillez saisir un numéro',
                'numero.max' => 'Le numéro est trop long'
            ]);

            $numero = trim($request->input('numero'));

            if (empty($numero)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Veuillez saisir un numéro valide'
                ], 400);
            }

            $sinistre = Sinistre::with(['gestionnaire', 'expertise'])
                ->where(function($query) use ($numero) {
                    $query->where('numero_sinistre', $numero)
                          ->orWhere('numero_police', $numero);
                })
                ->first();

            if (!$sinistre) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun sinistre trouvé avec ce numéro'
                ], 404);
            }

            $gestionnaireNom = null;
            if ($sinistre->gestionnaire) {
                $gestionnaireNom = $sinistre->gestionnaire->name;
            }

            $expertiseStatut = null;
            if ($sinistre->expertise) {
                $expertiseStatut = $sinistre->expertise->statut ?? null;
            }

            $response = [
                'success' => true,
                'sinistre' => [
                    'numero_sinistre' => $sinistre->numero_sinistre,
                    'statut' => $sinistre->statut,
                    'statut_libelle' => $sinistre->statut_libelle,
                    'statut_couleur' => $sinistre->statut_couleur,
                    'date_declaration' => $sinistre->created_at->format('d/m/Y'),
                    'date_sinistre' => $sinistre->date_sinistre ? $sinistre->date_sinistre->format('d/m/Y') : null,
                    'lieu_sinistre' => $sinistre->lieu_sinistre,
                    'gestionnaire' => $gestionnaireNom,
                    'jours_en_cours' => $sinistre->jours_en_cours,
                    'en_retard' => $sinistre->en_retard,
                    'expertise_requise' => $sinistre->statut === 'expertise_requise' || $sinistre->expertise !== null,
                ]
            ];

            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de sinistre', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer'
            ], 500);
        }
    }
}
