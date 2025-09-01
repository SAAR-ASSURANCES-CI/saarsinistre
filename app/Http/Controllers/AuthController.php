<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendPasswordResetEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        if (!in_array($user->role, ['admin', 'gestionnaire'])) {
            return back()->withErrors([
                'email' => 'Accès refusé. Cette interface est réservée aux gestionnaires et administrateurs.'
            ])->withInput();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            
            if ($user->password_expire_at && $user->password_expire_at->isFuture()) {
                return redirect()->route('gestionnaire.password.change')
                    ->with('info', 'Veuillez changer votre mot de passe temporaire.');
            }
            
            return redirect()->route('gestionnaires.dashboard');
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
        $this->validateLoginRequest($request);

        $credentials = $request->only('username', 'password');
        $user = $this->findUserByUsername($credentials['username']);

        if (!$user) {
            return $this->loginError("Aucun utilisateur trouvé avec ce nom d'utilisateur.", $request);
        }

        $validationResult = $this->validateUser($user, $credentials['password']);
        if ($validationResult !== true) {
            return $this->loginError($validationResult, $request);
        }

        $this->authenticateUser($user, $request);

        return $this->handleSuccessfulLogin($user);
    }

    private function validateLoginRequest(Request $request): void
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|min:6',
        ], [
            'username.required' => "Le nom d'utilisateur est obligatoire.",
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.',
        ]);
    }

    private function findUserByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    private function validateUser(User $user, string $password): string|bool
    {
        if (!$user->actif) {
            return 'Votre compte est désactivé. Veuillez contacter l\'administrateur.';
        }

        if ($user->role !== 'assure') {
            return 'Accès refusé. Cette interface est réservée aux assurés.';
        }

        if (!Hash::check($password, $user->password)) {
            return "Les identifiants fournis ne correspondent pas à nos enregistrements.";
        }

        if ($user->password_expire_at && $user->password_expire_at->isPast()) {
            return 'Votre mot de passe temporaire a expiré. Veuillez contacter l\'administrateur.';
        }

        return true;
    }

    private function authenticateUser(User $user, Request $request): void
    {
        Auth::login($user);
        $request->session()->regenerate();
    }

    private function handleSuccessfulLogin(User $user)
    {
        if ($user->password_expire_at && $user->password_expire_at->isFuture()) {
            return redirect()->route('assures.password.change')
                ->with('info', 'Veuillez changer votre mot de passe temporaire.');
        }

        return redirect()->route('assures.dashboard')
            ->with('success', 'Connexion réussie !');
    }

    private function loginError(string $message, Request $request)
    {
        return back()->withErrors(['username' => $message])
            ->withInput($request->except('password'));
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
        Auth::logout();
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

    /**
     * Affiche le formulaire de changement de mot de passe pour les gestionnaires
     */
    public function showChangePasswordForm()
    {
        return view('auth.change_password');
    }

    /**
     * Traite le changement de mot de passe pour les gestionnaires
     */
    public function changePassword(Request $request)
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

        return redirect()->route('gestionnaires.dashboard')->with('success', 'Votre mot de passe a été changé avec succès.');
    }

    /**
     * Affiche le formulaire de demande de réinitialisation
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Traite la demande de réinitialisation de mot de passe
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.exists' => 'Aucun compte trouvé avec cette adresse email.',
        ]);

        $user = User::where('email', $request->email)->first();
        
        $motDePasseTemporaire = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->password = Hash::make($motDePasseTemporaire);
        $user->password_temp = $motDePasseTemporaire;
        $user->password_expire_at = now()->addHours(24);
        $user->save();

        SendPasswordResetEmail::dispatch($user, $motDePasseTemporaire);

        return back()->with('success', 'Un nouveau mot de passe temporaire a été envoyé à votre adresse email.');
    }
}
