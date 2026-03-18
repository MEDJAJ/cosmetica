<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Vérifier si l'utilisateur est connecté et si son rôle correspond
        if ($request->user() && $request->user()->role !== $role) {
            return response()->json([
                'message' => "Accès refusé. Vous n'avez pas les droits nécessaires ($role)."
            ], 403);
        }

        return $next($request);
    }
}