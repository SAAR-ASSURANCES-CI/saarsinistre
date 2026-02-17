<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Feedback;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendSinistreStatusUpdateSms;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Sinistre::count(),
            'en_attente' => Sinistre::where('statut', 'en_attente')->count(),
            'traites' => Sinistre::whereIn('statut', ['regle', 'clos'])->count(),
            'expertise_requise' => Sinistre::where('statut', 'expertise_requise')->count(),
            'en_cours' => Sinistre::where('statut', 'en_cours')->count(),
            'en_retard' => Sinistre::where('en_retard', true)->count(),
            'en_attente_documents' => Sinistre::where('statut', 'en_attente_documents')->count(),
            'refuse' => Sinistre::where('statut', 'refuse')->count(),
            'feedbacks' => Feedback::count(),
            'feedbacks_completes' => Feedback::whereNotNull('note_service')->count(),
        ];

        $gestionnaires = User::gestionnaires()
            ->orderBy('nom_complet')
            ->get(['id', 'nom_complet']);

        return view('admin.dashboard', compact('stats', 'gestionnaires'));
    }

    public function getSinistreDetails($id)
    {
        $sinistre = Sinistre::with(['gestionnaire', 'documents', 'tiers.documents', 'vehicle'])
            ->findOrFail($id);

        return response()->json([
            'sinistre' => $sinistre,
        ]);
    }

    public function getSinistres(Request $request)
{
    $query = Sinistre::with(['gestionnaire:id,nom_complet'])
        ->select([
            'id',
            'numero_sinistre',
            'nom_assure',
            'email_assure',
            'telephone_assure',
            'numero_police',
            'date_sinistre',
            'heure_sinistre',
            'lieu_sinistre',
            'statut',
            'gestionnaire_id',
            'jours_en_cours',
            'en_retard',
            'montant_estime',
            'montant_regle',
            'circonstances',
            'date_affectation',
            'date_reglement',
            'created_at'
        ]);

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('numero_sinistre', 'LIKE', "%{$search}%")
                ->orWhere('nom_assure', 'LIKE', "%{$search}%")
                ->orWhere('telephone_assure', 'LIKE', "%{$search}%")
                ->orWhere('numero_police', 'LIKE', "%{$search}%");
        });
    }

    if ($request->filled('statut')) {
        switch ($request->statut) {
            case 'en_retard':
                $query->where('en_retard', true);
                break;
                
            case 'regle':
                $query->whereIn('statut', ['regle', 'clos']);
                break;
                
            default:
                $query->where('statut', $request->statut);
                break;
        }
    }

    if ($request->filled('gestionnaire_id')) {
        if ($request->gestionnaire_id === 'null') {
            $query->whereNull('gestionnaire_id');
        } else {
            $query->where('gestionnaire_id', $request->gestionnaire_id);
        }
    }

    $query->orderBy('created_at', 'desc');

    $perPage = $request->get('per_page', 10);
    $sinistres = $query->paginate($perPage);

    $sinistres->getCollection()->each(function ($sinistre) {
        $sinistre->calculerJoursEnCours();
    });

    $counts = [
        'tous' => Sinistre::count(),
        'en_attente' => Sinistre::where('statut', 'en_attente')->count(),
        'en_cours' => Sinistre::where('statut', 'en_cours')->count(),
        'regle' => Sinistre::whereIn('statut', ['regle', 'clos'])->count(),
        'expertise_requise' => Sinistre::where('statut', 'expertise_requise')->count(),
        'en_attente_documents' => Sinistre::where('statut', 'en_attente_documents')->count(),
        'refuse' => Sinistre::where('statut', 'refuse')->count(),
        'en_retard' => Sinistre::where('en_retard', true)->count(),
        'pret_reglement' => Sinistre::where('statut', 'pret_reglement')->count(),
    ];

    return response()->json([
        'data' => $sinistres->items(),
        'current_page' => $sinistres->currentPage(),
        'last_page' => $sinistres->lastPage(),
        'per_page' => $sinistres->perPage(),
        'total' => $sinistres->total(), 
        'from' => $sinistres->firstItem(),
        'to' => $sinistres->lastItem(),
        'counts' => $counts,
    ]);
}

    public function assignerGestionnaire(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'gestionnaire_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value === 'self') {
                        return;
                    }

                    if ($value && !User::where('id', $value)->where('role', 'gestionnaire')->exists()) {
                        $fail('Le gestionnaire sélectionné est invalide.');
                    }
                }
            ]
        ]);

        $gestionnaireId = null;

        if ($request->gestionnaire_id === 'self') {
            $gestionnaireId = Auth::id();
        } elseif ($request->gestionnaire_id) {
            $gestionnaireId = $request->gestionnaire_id;
        }

        $sinistre->assignerGestionnaire($gestionnaireId);

        return response()->json([
            'success' => true,
            'message' => $gestionnaireId ? 'Gestionnaire affecté avec succès' : 'Sinistre désaffecté avec succès',
            'sinistre' => $sinistre->fresh()->load('gestionnaire:id,nom_complet')
        ]);
    }


    public function changerStatut(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'statut' => 'required|in:en_attente,en_cours,expertise_requise,en_attente_documents,pret_reglement,regle,refuse,clos',
            'commentaire' => 'nullable|string|max:1000'
        ]);

        $ancienStatut = $sinistre->statut;
        $sinistre->update([
            'statut' => $request->statut
        ]);

        if ($request->statut === 'regle' && $request->filled('montant_regle')) {
            $sinistre->reglerSinistre($request->montant_regle);
        }

        if ($ancienStatut !== $request->statut) {
            SendSinistreStatusUpdateSms::dispatch($sinistre, $ancienStatut)
                ->delay(now()->addSeconds(10));
        }

        return response()->json([
            'success' => true,
            'message' => 'Statut modifié avec succès',
            'sinistre' => $sinistre->fresh()->load('gestionnaire:id,nom_complet')
        ]);
    }

    public function getDetails(Sinistre $sinistre)
    {
        $sinistre->load(['gestionnaire:id,nom_complet', 'documents', 'tiers.documents', 'vehicle']);

        $stats = [
            'pourcentage_documents_verifies' => $sinistre->pourcentage_documents_verifies,
            'tous_documents_verifies' => $sinistre->tous_documents_verifies,
            'date_limite' => $sinistre->date_limite->format('Y-m-d'),
            'est_urgent' => $sinistre->est_urgent,
            'statut_libelle' => $sinistre->statut_libelle,
            'statut_couleur' => $sinistre->statut_couleur,
            'peut_etre_modifie' => $sinistre->peutEtreModifie()
        ];

        return response()->json([
            'success' => true,
            'sinistre' => $sinistre,
            'stats' => $stats
        ]);
    }

    public function getStats()
    {
        $stats = [
            'total' => Sinistre::count(),
            'en_attente' => Sinistre::where('statut', 'en_attente')->count(),
            'en_cours' => Sinistre::where('statut', 'en_cours')->count(),
            'traites' => Sinistre::whereIn('statut', ['regle', 'clos'])->count(),
            'en_retard' => Sinistre::where('en_retard', true)->count(),
            'expertise_requise' => Sinistre::where('statut', 'expertise_requise')->count(),
            'en_attente_documents' => Sinistre::where('statut', 'en_attente_documents')->count(),
            'refuse' => Sinistre::where('statut', 'refuse')->count(),
        ];

        $statsByGestionnaire = Sinistre::select('gestionnaire_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN en_retard = 1 THEN 1 ELSE 0 END) as en_retard')
            ->with('gestionnaire:id,nom_complet')
            ->whereNotNull('gestionnaire_id')
            ->groupBy('gestionnaire_id')
            ->get();

        return response()->json([
            'stats' => $stats,
            'stats_by_gestionnaire' => $statsByGestionnaire
        ]);
    }

    public function searchSinistres(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2'
        ]);

        $query = $request->input('query');

        $sinistres = Sinistre::where('numero_sinistre', 'LIKE', "%{$query}%")
            ->orWhere('nom_assure', 'LIKE', "%{$query}%")
            ->orWhere('telephone_assure', 'LIKE', "%{$query}%")
            ->orWhere('numero_police', 'LIKE', "%{$query}%")
            ->with('gestionnaire:id,nom_complet')
            ->limit(10)
            ->get(['id', 'numero_sinistre', 'nom_assure', 'telephone_assure', 'statut']);

        return response()->json($sinistres);
    }

    public function getSinistresEnRetard()
    {
        $sinistres = Sinistre::where('en_retard', true)
            ->with('gestionnaire:id,nom_complet')
            ->orderBy('jours_en_cours', 'desc')
            ->get();

        return response()->json($sinistres);
    }

    public function markNotificationsAsRead(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Notifications marquées comme lues'
        ]);
    }

    public function getNotifications()
    {
        $notifications = [];

        $sinistresEnRetard = Sinistre::where('en_retard', true)->count();
        if ($sinistresEnRetard > 0) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Sinistres en retard',
                'message' => "{$sinistresEnRetard} sinistre(s) sont en retard de traitement",
                'count' => $sinistresEnRetard,
                'created_at' => now()
            ];
        }

        $sinistresNonAssignes = Sinistre::whereNull('gestionnaire_id')
            ->where('statut', 'en_attente')
            ->count();
        if ($sinistresNonAssignes > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Sinistres non assignés',
                'message' => "{$sinistresNonAssignes} nouveau(x) sinistre(s) en attente d'affectation",
                'count' => $sinistresNonAssignes,
                'created_at' => now()
            ];
        }

        $sinistresExpertise = Sinistre::where('statut', 'expertise_requise')->count();
        if ($sinistresExpertise > 0) {
            $notifications[] = [
                'type' => 'urgent',
                'title' => 'Expertises requises',
                'message' => "{$sinistresExpertise} sinistre(s) nécessitent une expertise",
                'count' => $sinistresExpertise,
                'created_at' => now()
            ];
        }

        $sinistresReglement = Sinistre::where('statut', 'pret_reglement')->count();
        if ($sinistresReglement > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'Règlements en attente',
                'message' => "{$sinistresReglement} sinistre(s) prêts pour règlement",
                'count' => $sinistresReglement,
                'created_at' => now()
            ];
        }

        return response()->json([
            'notifications' => $notifications,
            'total_unread' => count($notifications)
        ]);
    }
}
