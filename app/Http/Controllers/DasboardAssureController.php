<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DasboardAssureController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $sinistres = $user
            ->sinistresAssure()
            ->with(['gestionnaire:id,nom_complet,email'])
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('assures.dashboard', compact('sinistres'));
    }
}
