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
    public function index(Request $request): View
    {
        $gestionnairesQuery = User::whereIn('role', ['gestionnaire', 'admin']);
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
            'nom_complet' => $request->input('nom_complet'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'numero_assure' => $request->input('numero_assure'),
            'password' => Hash::make($request->input('password')),
            'actif' => true,
            'limite_sinistres' => $request->input('role') === 'assure' ? 5 : ($request->input('role') === 'gestionnaire' ? 15 : 20),
        ]);

        return redirect()->route('dashboard.users')->with('success', 'Utilisateur créé avec succès');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->getKey(),
            'role' => 'required|in:admin,gestionnaire,user',
            'numero_assure' => 'nullable|string|max:50|unique:users,numero_assure,' . $user->getKey(),
            'actif' => 'boolean',
            'limite_sinistres' => 'nullable|integer|min:1',
        ]);

        $user->update([
            'nom_complet' => $request->input('nom_complet'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'numero_assure' => $request->input('role') === 'user' ? $request->input('numero_assure') : null,
            'actif' => $request->input('actif', false),
            'limite_sinistres' => $request->input('limite_sinistres'),
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
