<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScannerRole
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures only scanner role users can access scanner pages
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Only scanner roles can access scanner pages
        if (!$user->hasRole(['wristband_exchange_officer', 'wristband_validator', 'super_admin'])) {
            abort(403, 'You do not have permission to access scanner pages');
        }

        return $next($request);
    }
}
