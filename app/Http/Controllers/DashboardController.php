<?php

namespace App\Http\Controllers;

use App\Models\Sinistre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Afficher le dashboard principal
     */
    public function index()
    {
        // Statistiques générales
        $stats = [
            'total' => Sinistre::count(),
            'en_attente' => Sinistre::where('statut', 'en_attente')->count(),
            'traites' => Sinistre::whereIn('statut', ['regle', 'clos'])->count(),
            'en_retard' => Sinistre::where('en_retard', true)->count(),
        ];

        // Liste des gestionnaires pour les filtres
        $gestionnaires = User::select('id', 'nom_complet')
            ->whereHas('sinistres')
            ->orWhere('id', Auth::id())
            ->orderBy('nom_complet')
            ->get();

        return view('admin.dashboard', compact('stats', 'gestionnaires'));
    }

    /**
     * API pour récupérer les sinistres avec filtres et recherche
     */
    public function getSinistres(Request $request)
    {
        $query = Sinistre::with(['gestionnaire:id,name'])
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

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero_sinistre', 'LIKE', "%{$search}%")
                    ->orWhere('nom_assure', 'LIKE', "%{$search}%")
                    ->orWhere('telephone_assure', 'LIKE', "%{$search}%")
                    ->orWhere('numero_police', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Filtre par gestionnaire
        if ($request->filled('gestionnaire_id')) {
            if ($request->gestionnaire_id === 'null') {
                $query->whereNull('gestionnaire_id');
            } else {
                $query->where('gestionnaire_id', $request->gestionnaire_id);
            }
        }

        // Tri par défaut (plus récents en premier)
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = $request->get('per_page', 10);
        $sinistres = $query->paginate($perPage);

        // Mettre à jour les jours en cours pour tous les sinistres récupérés
        $sinistres->getCollection()->each(function ($sinistre) {
            $sinistre->calculerJoursEnCours();
        });

        return response()->json($sinistres);
    }

    /**
     * Affecter un gestionnaire à un sinistre
     */
    public function assignerGestionnaire(Request $request, Sinistre $sinistre)
    {
        $request->validate([
            'gestionnaire_id' => 'nullable|exists:users,id'
        ]);

        $gestionnaireId = $request->gestionnaire_id;

        // Si c'est "self", utiliser l'ID de l'utilisateur connecté
        if ($request->gestionnaire_id === 'self') {
            $gestionnaireId = Auth::id();
        }

        // Utiliser la méthode du modèle
        $sinistre->assignerGestionnaire($gestionnaireId);

        return response()->json([
            'success' => true,
            'message' => 'Gestionnaire affecté avec succès',
            'sinistre' => $sinistre->fresh()->load('gestionnaire:id,name')
        ]);
    }

    /**
     * Changer le statut d'un sinistre
     */
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

        // Si le statut devient "réglé", on peut demander le montant
        if ($request->statut === 'regle' && $request->filled('montant_regle')) {
            $sinistre->reglerSinistre($request->montant_regle);
        }

        // Ici vous pourriez enregistrer l'historique des changements
        // $this->enregistrerHistorique($sinistre, $ancienStatut, $request->statut, $request->commentaire);

        return response()->json([
            'success' => true,
            'message' => 'Statut modifié avec succès',
            'sinistre' => $sinistre->fresh()->load('gestionnaire:id,name')
        ]);
    }

    /**
     * Obtenir les détails complets d'un sinistre
     */
    public function getDetails(Sinistre $sinistre)
    {
        $sinistre->load(['gestionnaire:id,name', 'documents']);

        // Calculer les informations supplémentaires en utilisant les attributs du modèle
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

    /**
     * Mettre à jour les statistiques du dashboard
     */
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
        ];

        // Statistiques par gestionnaire
        $statsByGestionnaire = Sinistre::select('gestionnaire_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN en_retard = 1 THEN 1 ELSE 0 END) as en_retard')
            ->with('gestionnaire:id,name')
            ->whereNotNull('gestionnaire_id')
            ->groupBy('gestionnaire_id')
            ->get();

        return response()->json([
            'stats' => $stats,
            'stats_by_gestionnaire' => $statsByGestionnaire
        ]);
    }

    /**
     * Recherche rapide de sinistres
     */
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
            ->with('gestionnaire:id,name')
            ->limit(10)
            ->get(['id', 'numero_sinistre', 'nom_assure', 'telephone_assure', 'statut']);

        return response()->json($sinistres);
    }

    /**
     * Obtenir les sinistres en retard
     */
    public function getSinistresEnRetard()
    {
        $sinistres = Sinistre::where('en_retard', true)
            ->with('gestionnaire:id,name')
            ->orderBy('jours_en_cours', 'desc')
            ->get();

        return response()->json($sinistres);
    }

    /**
     * Marquer les notifications comme lues
     */
    public function markNotificationsAsRead(Request $request)
    {
        // Ici vous pouvez implémenter la logique pour marquer les notifications comme lues
        // Par exemple, mettre à jour une table notifications ou un champ dans la table users

        // Exemple : si vous avez une table notifications
        /*
        \App\Models\Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        */

        return response()->json([
            'success' => true,
            'message' => 'Notifications marquées comme lues'
        ]);
    }

    /**
     * Obtenir les notifications non lues
     */
    public function getNotifications()
    {
        // Exemple de notifications basées sur les sinistres
        $notifications = [];

        // Sinistres en retard
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

        // Nouveaux sinistres non assignés
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

        // Sinistres nécessitant une expertise
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

        // Sinistres prêts pour règlement
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
