<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPasswordExpiry
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->password_expire_at && $user->password_expire_at->isFuture()) {

            if (!$request->routeIs('gestionnaire.password.change') && !$request->routeIs('gestionnaire.password.change.post')) {
                if (in_array($user->role, ['admin', 'gestionnaire'])) {
                    return redirect()->route('gestionnaire.password.change')
                        ->with('info', 'Vous devez changer votre mot de passe temporaire avant de continuer.');
                } elseif ($user->role === 'assure') {
                    return redirect()->route('assure.password.change')
                        ->with('info', 'Vous devez changer votre mot de passe temporaire avant de continuer.');
                }
            }
        }

        return $next($request);
    }
}
