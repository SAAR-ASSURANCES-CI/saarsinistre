<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\PasswordResetCode;
use App\Models\User;
use App\Models\Sinistre;
use App\Services\OrangeService;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    protected $orangeService;

    public function __construct(OrangeService $orangeService)
    {
        $this->orangeService = $orangeService;
    }

    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showRequestForm()
    {
        return view('auth.forgot_password_assure');
    }

    /**
     * Traiter la demande de réinitialisation
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'telephone' => 'required|string|min:10'
        ], [
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.min' => 'Le numéro de téléphone doit contenir au moins 10 chiffres.'
        ]);

        $telephone = $request->telephone;

        Log::info('Tentative de réinitialisation de mot de passe', [
            'telephone_saisi' => $request->telephone,
            'telephone_utilise' => $telephone
        ]);

        $sinistre = Sinistre::where('telephone_assure', $telephone)
            ->whereNotNull('assure_id')
            ->first();
        
        Log::info('Recherche de sinistre', [
            'telephone_recherche' => $telephone,
            'sinistre_trouve' => $sinistre ? true : false,
            'sinistre_id' => $sinistre ? $sinistre->id : null,
            'assure_id' => $sinistre ? $sinistre->assure_id : null
        ]);
        
        if (!$sinistre) {
    
            $sinistreSansAssure = Sinistre::where('telephone_assure', $telephone)->first();
            
            if ($sinistreSansAssure) {
                Log::warning('Sinistre trouvé mais sans assure_id', [
                    'sinistre_id' => $sinistreSansAssure->id,
                    'telephone' => $sinistreSansAssure->telephone_assure,
                    'assure_id' => $sinistreSansAssure->assure_id
                ]);
            }
            
            return back()->withErrors([
                'telephone' => 'Aucun compte assuré trouvé avec ce numéro de téléphone. Vérifiez le numéro saisi ou contactez l\'assurance.'
            ])->withInput();
        }

        $user = User::find($sinistre->assure_id);
        if (!$user) {
            return back()->withErrors([
                'telephone' => 'Compte utilisateur non trouvé. Veuillez contacter l\'assurance.'
            ])->withInput();
        }

        try {
            $resetCode = PasswordResetCode::createForPhone($telephone);

            $message = "SAAR ASSURANCES\nVotre code de réinitialisation est: {$resetCode->code}\nCe code expire dans 10 minutes.";
            

            $telephoneSMS = $this->formatPhoneNumber($telephone);
            $this->orangeService->sendSMS($telephoneSMS, $message, 'SAAR CI');

        return redirect()->route('password.reset.verify.assure')
            ->with('success', 'Un code de vérification a été envoyé par SMS à votre numéro de téléphone.')
            ->with('telephone', $telephone);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du code de réinitialisation', [
                'telephone' => $telephone,
                'error' => $e->getMessage()
            ]);

            return back()->withErrors([
                'telephone' => 'Erreur lors de l\'envoi du SMS. Veuillez réessayer.'
            ])->withInput();
        }
    }

    /**
     * Afficher le formulaire de vérification du code
     */
    public function showVerifyForm(Request $request)
    {
        // Pas besoin de vérifier de session - on peut toujours afficher le formulaire
        return view('auth.verify_reset_code');
    }

    /**
     * Vérifier le code et afficher le formulaire de nouveau mot de passe
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'telephone' => 'required|string|min:10' 
        ], [
            'code.required' => 'Le code de vérification est obligatoire.',
            'code.size' => 'Le code doit contenir exactement 6 chiffres.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.'
        ]);

        $telephone = $request->telephone;
        $code = $request->code;

        Log::info('Vérification du code', [
            'telephone_formulaire' => $telephone,
            'code_saisi' => $code
        ]);

        
        $resetCode = PasswordResetCode::where('telephone', $telephone)
            ->where('code', $code)
            ->valid()
            ->first();

        if (!$resetCode) {
            return back()->withErrors([
                'code' => 'Code invalide ou expiré. Veuillez vérifier le code ou demander un nouveau code.'
            ])->withInput();
        }

        $resetCode->markAsUsed();

        $request->session()->put('reset_telephone', $telephone);

        Log::info('Code vérifié avec succès', [
            'telephone' => $telephone,
            'code' => $code
        ]);

        return redirect()->route('password.reset.new.assure')
            ->with('success', 'Code vérifié avec succès. Vous pouvez maintenant définir votre nouveau mot de passe.');
    }

    /**
     * Afficher le formulaire de nouveau mot de passe
     */
    public function showNewPasswordForm(Request $request)
    {
        if (!$request->session()->has('reset_telephone')) {
            return redirect()->route('password.reset.request.assure');
        }

        return view('auth.new_password_assure');
    }

    /**
     * Traiter le nouveau mot de passe
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        $telephone = $request->session()->get('reset_telephone');

        if (!$telephone) {
            return redirect()->route('password.reset.request.assure');
        }

        $sinistre = Sinistre::where('telephone_assure', $telephone)->first();
        
        if (!$sinistre || !$sinistre->assure_id) {
            return redirect()->route('password.reset.request.assure')
                ->withErrors(['error' => 'Utilisateur non trouvé.']);
        }

        $user = User::find($sinistre->assure_id);
        
        if (!$user) {
            return redirect()->route('password.reset.request.assure')
                ->withErrors(['error' => 'Utilisateur non trouvé.']);
        }

        $user->password = Hash::make($request->password);
        $user->password_expire_at = null;
        $user->password_temp = null;
        $user->save();

        $request->session()->forget('reset_telephone');

        return redirect()->route('login.assure')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }

    /**
     * Formater le numéro de téléphone
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        $cleanNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (str_starts_with($cleanNumber, '+225')) {
            return $cleanNumber;
        }

        if (str_starts_with($cleanNumber, '225')) {
            return '+' . $cleanNumber;
        }

        if (str_starts_with($cleanNumber, '0')) {
            return '+225' . $cleanNumber;
        }

        return '+225' . $cleanNumber;
    }
}
