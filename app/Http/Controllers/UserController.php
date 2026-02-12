<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Jobs\SendUserCredentialsEmail;

class UserController extends Controller
{
    /**
     * Display a listing of the resource or handle AJAX filtering.
     */
    public function index(Request $request): JsonResponse|View
    {
        $gestionnairesQuery = User::whereIn('role', ['gestionnaire', 'admin', 'expert']);
        $assuresQuery = User::where('role', 'assure');

        if ($request->has('search')) {
            $search = $request->input('search');
            $gestionnairesQuery->where(function ($query) use ($search) {
                $query->where('nom_complet', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });

            $assuresQuery->where(function ($query) use ($search) {
                $query->where('nom_complet', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('numero_assure', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('role')) {
            $gestionnairesQuery->where('role', $request->input('role'));
        }

        if ($request->has('status')) {
            $status = $request->input('status') == '1';
            $gestionnairesQuery->where('actif', $status);
            $assuresQuery->where('actif', $status);
        }

        $gestionnaires = $gestionnairesQuery->paginate(10)->appends($request->query());
        $assures = $assuresQuery->paginate(10)->appends($request->query());

        if ($request->ajax()) {
            return response()->json([
                'gestionnaires' => view('users.partials.gestionnaires-table', compact('gestionnaires'))->render(),
                'assures' => view('users.partials.assures-table', compact('assures'))->render(),
                'gestionnaires_pagination' => $gestionnaires->links()->render(),
                'assures_pagination' => $assures->links()->render(),
            ]);
        }

        return view('users.index', compact(['gestionnaires', 'assures']));
    }

    public function edit(User $user): JsonResponse
    {
        return response()->json([
            'id' => $user->id,
            'nom_complet' => $user->nom_complet,
            'email' => $user->email,
            'role' => $user->role,
            'numero_assure' => $user->numero_assure,
            'actif' => (bool)$user->actif
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,gestionnaire,expert',
        ]);

        $motDePasseTemporaire = $this->generateTemporaryPassword();
        
        $user = User::create([
            'nom_complet' => $request->input('nom_complet'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => Hash::make($motDePasseTemporaire),
            'password_temp' => $motDePasseTemporaire,
            'password_expire_at' => now()->addHours(48),
            'actif' => true,
        ]);

        SendUserCredentialsEmail::dispatch($user, $motDePasseTemporaire);

        return redirect()->route('gestionnaires.dashboard.users.index')->with('success', 'Utilisateur créé avec succès. Les informations de connexion ont été envoyées par email.');
    }

    /**
     * Génère un mot de passe temporaire
     */
    private function generateTemporaryPassword(): string
    {
        return str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,gestionnaire,assure,expert',
            'numero_assure' => 'nullable|string|max:50|unique:users,numero_assure,' . $user->id,
        ]);

        $user->update([
            'nom_complet' => $request->nom_complet,
            'email' => $request->email,
            'role' => $request->role,
            'numero_assure' => $request->role === 'assure' ? $request->numero_assure : null,
        ]);

        return response()->json(['success' => 'Utilisateur mis à jour avec succès']);
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('gestionnaires.dashboard.users.index')->with('success', 'Utilisateur supprimé avec succès');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update(['actif' => !$user->actif]);
        return back()->with('success', 'Statut utilisateur mis à jour');
    }
}
