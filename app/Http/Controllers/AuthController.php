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
     *  connexion des gestionnaires/admins
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => "L'adresse email est obligatoire.",
            'email.email' => "L'adresse email doit être valide.",
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
            return redirect()->route('dashboard');
        }
        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->withInput($request->except('password'));
    }

    /**
     * Traiter la connexion des assurés
     */
    public function loginAssure(Request $request)
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
        if (!$user) {
            return back()->withErrors([
                'username' => "Aucun utilisateur trouvé avec ce nom d'utilisateur.",
            ])->withInput($request->except('password'));
        }
        if (!$user->actif) {
            return back()->withErrors([
                'username' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.',
            ])->withInput($request->except('password'));
        }
       
        if (Hash::check($credentials['password'], $user->password)) {
            
            if ($user->password_expire_at && $user->password_expire_at->isFuture()) {
            
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('assure.password.change')->with('info', 'Veuillez changer votre mot de passe temporaire.');
            }
            
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                return redirect()->route('assures.dashboard')->with('success', 'Connexion réussie !');
            }
        }
        return back()->withErrors([
            'username' => "Les identifiants fournis ne correspondent pas à nos enregistrements.",
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

        return redirect()->route('login');
    }

    /**
     * Déconnexion des assurés
     */
    public function logoutAssure(Request $request)
    {
        Auth::guard('assure')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.assure');
    }

    /**
     * Affiche le formulaire de changement de mot de passe pour l'assuré
     */
    public function showChangePasswordFormAssure()
    {
        return view('auth.change_password_assure');
    }

    /**
     * Traite le changement de mot de passe pour l'assuré
     */
    public function changePasswordAssure(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ]);
        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_expire_at = null;
        $user->password_temp = null;
        $user->save();
        return redirect()->route('assures.dashboard')->with('success', 'Votre mot de passe a été changé avec succès.');
    }
}
