<?php

namespace App\Http\Controllers;

use App\Models\Sinistre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DasboardAssureController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sinistres = $user
            ->sinistresAssure()
            ->with(['gestionnaire:id,nom_complet,email', 'tiers'])
            ->orderByDesc('created_at')
            ->paginate(10);

        // Vérifier s'il y a des sinistres nécessitant un feedback
        $sinistresNecessitantFeedback = $user
            ->sinistresAssure()
            ->whereIn('statut', ['clos', 'regle'])
            ->whereDoesntHave('feedbacks', function($query) use ($user) {
                $query->where('assure_id', $user->id);
            })
            ->get();

        return view('assures.dashboard', compact('sinistres', 'sinistresNecessitantFeedback'));
    }
}
