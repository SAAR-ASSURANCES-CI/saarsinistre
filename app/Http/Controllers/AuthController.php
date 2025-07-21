<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Afficher le formulaire de connexion pour les assurés
     */
    public function showLoginAssureForm()
    {
        return view('auth.login_assure');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        if ($request->has('username')) {
            return $this->loginAssure($request);
        }
        return $this->loginAdminGestionnaire($request);
    }

    private function loginAssure(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|min:6',
        ], [
            'username.required' => "Le nom d'utilisateur est obligatoire.",
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);
        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();
        if ($user && !$user->actif) {
            return back()->withErrors([
                'username' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
            ])->withInput($request->except('password'));
        }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'assure') {
                return redirect()->route('assures.dashboard')->with('success', 'Connexion réussie !');
            } else {
                Auth::logout();
                return back()->withErrors([
                    'username' => "Vous n'êtes pas autorisé à accéder à cet espace.",
                ]);
            }
        }
        return back()->withErrors([
            'username' => "Les identifiants fournis ne correspondent pas à nos enregistrements.",
        ])->withInput($request->except('password'));
    }

    private function loginAdminGestionnaire(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();
        if ($user && !$user->actif) {
            return back()->withErrors([
                'email' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
            ])->withInput($request->except('password'));
        }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ($user->role === 'assure') {
                return redirect()->route('assures.dashboard')->with('success', 'Connexion réussie !');
            } else {
                return redirect()->route('dashboard')->with('success', 'Connexion réussie !');
            }
        }
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->except('password'));
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
