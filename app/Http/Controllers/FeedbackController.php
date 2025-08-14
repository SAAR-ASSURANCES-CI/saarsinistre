<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Afficher le formulaire de feedback pour un sinistre
     */
    public function showForm(Sinistre $sinistre)
    {
        if (Auth::id() !== $sinistre->assure_id) {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($sinistre->statut, ['clos', 'regle'])) {
            abort(400, 'Le feedback n\'est disponible que pour les sinistres clos ou réglés');
        }

        $feedback = Feedback::where('sinistre_id', $sinistre->id)
            ->where('assure_id', Auth::id())
            ->first();

        return view('assures.feedback.form', compact('sinistre', 'feedback'));
    }

    /**
     * Enregistrer le feedback de l'assuré.
     */
    public function store(Request $request, Sinistre $sinistre)
    {
        if (Auth::id() !== $sinistre->assure_id) {
            abort(403, 'Accès non autorisé');
        }

        if (!in_array($sinistre->statut, ['clos', 'regle'])) {
            abort(400, 'Le feedback n\'est disponible que pour les sinistres clos ou réglés');
        }

        $validator = Validator::make($request->all(), [
            'note_service' => 'required|integer|between:1,5',
            'humeur_emoticon' => 'required|string|max:10',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Feedback::updateOrCreate(
            [
                'sinistre_id' => $sinistre->id,
                'assure_id' => Auth::id(),
            ],
            [
                'note_service' => $request->note_service,
                'humeur_emoticon' => $request->humeur_emoticon,
                'commentaire' => $request->commentaire,
                'date_feedback' => now(),
                'envoye_automatiquement' => false,
            ]
        );

        return redirect()->route('assures.dashboard')
            ->with('success', 'Merci pour votre feedback ! Votre avis nous aide à améliorer nos services.');
    }

    /**
     * Afficher la liste des feedbacks pour les gestionnaires
     */
    public function index(Request $request)
    {
        $query = Feedback::with(['sinistre', 'assure'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('note_service')) {
            $query->where('note_service', $request->note_service);
        }

        if ($request->filled('humeur_emoticon')) {
            $query->where('humeur_emoticon', $request->humeur_emoticon);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $perPage = $request->get('per_page', 15);
        $feedbacks = $query->paginate($perPage);

        $stats = [
            'total' => Feedback::count(),
            'moyenne_note' => round(Feedback::whereNotNull('note_service')->avg('note_service'), 1),
            'taux_reponse' => Feedback::whereNotNull('note_service')->count() > 0 ? round((Feedback::whereNotNull('note_service')->count() / Sinistre::whereIn('statut', ['clos', 'regle'])->count()) * 100, 1) : 0,
            'automatiques' => Sinistre::whereIn('statut', ['clos', 'regle'])->whereDoesntHave('feedbacks')->count(),
            'tres_satisfaits' => Feedback::where('humeur_emoticon', '😊')->count(),
            'satisfaits' => Feedback::where('humeur_emoticon', '🙂')->count(),
            'neutres' => Feedback::where('humeur_emoticon', '😐')->count(),
            'mecontents' => Feedback::where('humeur_emoticon', '😕')->count(),
            'tres_mecontents' => Feedback::where('humeur_emoticon', '😠')->count(),
        ];

        return view('admin.feedback.index', compact('feedbacks', 'stats'));
    }

    /**
     * Afficher les détails d'un feedback
     */
    public function show(Feedback $feedback)
    {
        return view('admin.feedback.show', compact('feedback'));
    }

    /**
     * Exporter les feedbacks en CSV
     */
    public function export(Request $request)
    {
        $query = Feedback::with(['sinistre', 'assure']);

        if ($request->filled('note_service')) {
            $query->where('note_service', $request->note_service);
        }

        if ($request->filled('humeur_emoticon')) {
            $query->where('humeur_emoticon', $request->humeur_emoticon);
        }

        $feedbacks = $query->orderBy('created_at', 'desc')->get();

        $filename = 'feedbacks_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($feedbacks) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Numéro Sinistre', 'Assuré', 'Note Service', 'Humeur', 
                'Commentaire', 'Date Feedback', 'Envoyé Automatiquement'
            ]);

            foreach ($feedbacks as $feedback) {
                fputcsv($file, [
                    $feedback->id,
                    $feedback->sinistre->numero_sinistre,
                    $feedback->assure->nom_complet ?? $feedback->assure->name,
                    $feedback->note_service,
                    $feedback->humeur_emoticon,
                    $feedback->commentaire,
                    $feedback->date_feedback?->format('d/m/Y H:i'),
                    $feedback->envoye_automatiquement ? 'Oui' : 'Non'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
