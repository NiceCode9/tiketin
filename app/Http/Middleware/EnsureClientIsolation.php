<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClientIsolation
{
    /**
     * Handle an incoming request.
     *
     * This middleware ensures that client users can only access their own data
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Only apply to client role users
        if ($user && $user->hasRole('client')) {
            // The ClientScope global scope will handle filtering
            // This middleware is just an additional layer of security
            
            // If accessing a specific resource by ID, verify ownership
            if ($request->route('event')) {
                $event = $request->route('event');
                if ($event->client_id !== $user->client_id) {
                    abort(403, 'Unauthorized access to this resource');
                }
            }

            if ($request->route('order')) {
                $order = $request->route('order');
                if ($order->event->client_id !== $user->client_id) {
                    abort(403, 'Unauthorized access to this resource');
                }
            }

            if ($request->route('ticket')) {
                $ticket = $request->route('ticket');
                if ($ticket->order->event->client_id !== $user->client_id) {
                    abort(403, 'Unauthorized access to this resource');
                }
            }

            if ($request->route('promoCode')) {
                $promoCode = $request->route('promoCode');
                if ($promoCode->event->client_id !== $user->client_id) {
                    abort(403, 'Unauthorized access to this resource');
                }
            }
        }

        return $next($request);
    }
}
