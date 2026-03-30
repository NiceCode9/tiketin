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
            return redirect()->route('scanner.login');
        }

        // Only scanner roles can access scanner pages
        if (!$user->hasRole(['wristband_exchange_officer', 'wristband_validator', 'super_admin'])) {
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman scanner ini.');
        }

        return $next($request);
    }
}
