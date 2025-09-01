<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {

            if ($request->is('assures/*') || $request->is('sinistres/*')) {
                return redirect()->route('login.assure');
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($user->role === 'assure') {
                return redirect()->route('login.assure')->withErrors([
                    'username' => 'Accès non autorisé. Vous avez été déconnecté.',
                ]);
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Accès non autorisé. Vous avez été déconnecté.',
            ]);
        }

        return $next($request);
    }
}
