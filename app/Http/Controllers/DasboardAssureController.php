<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DasboardAssureController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sinistres = $user->sinistresAssure()->orderByDesc('created_at')->paginate(10);
        return view('assures.dashboard', compact('sinistres'));
    }
}
