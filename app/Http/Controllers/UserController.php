<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    /**************************/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
    {
        $gestionnaires = User::whereIn('role', ['gestionnaire', 'admin'])->get();
        $assures = User::where('role', 'assure')->get();

        return view('users.index', compact(['gestionnaires', 'assures']));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,gestionnaire,user',
            'numero_assure' => 'nullable|string|max:50|unique:users',
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'nom_complet' => $request->nom_complet,
            'email' => $request->email,
            'role' => $request->role,
            'numero_assure' => $request->numero_assure,
            'password' => Hash::make($request->password),
            'actif' => true,
            'limite_sinistres' => $request->role === 'assure' ? 5 : ($request->role === 'gestionnaire' ? 15 : 20),
        ]);

        return redirect()->route('dashboard.users')->with('success', 'Utilisateur créé avec succès');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,gestionnaire,user',
            'numero_assure' => 'nullable|string|max:50|unique:users,numero_assure,' . $user->id,
            'actif' => 'boolean',
            'limite_sinistres' => 'nullable|integer|min:1',
        ]);

        $user->update([
            'nom_complet' => $request->nom_complet,
            'email' => $request->email,
            'role' => $request->role,
            'numero_assure' => $request->role === 'user' ? $request->numero_assure : null,
            'actif' => $request->actif ?? false,
            'limite_sinistres' => $request->limite_sinistres,
        ]);

        return redirect()->route('dashboard.users')->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('dashboard.users')->with('success', 'Utilisateur supprimé avec succès');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        $user->update(['actif' => !$user->actif]);
        return back()->with('success', 'Statut utilisateur mis à jour');
    }
}
