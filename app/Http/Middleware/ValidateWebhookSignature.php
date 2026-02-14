<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateWebhookSignature
{
    /**
     * Handle an incoming request.
     *
     * This middleware validates Midtrans webhook signatures
     */
    public function handle(Request $request, Closure $next): Response
    {
        $payload = $request->all();

        // Verify required fields exist
        if (! isset($payload['order_id'], $payload['status_code'], $payload['gross_amount'], $payload['signature_key'])) {
            abort(400, 'Invalid webhook payload');
        }

        // Verify signature
        $serverKey = config('midtrans.server_key');
        $orderId = $payload['order_id'];
        $statusCode = $payload['status_code'];
        $grossAmount = $payload['gross_amount'];

        $signatureKey = hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey);

        if ($signatureKey !== $payload['signature_key']) {
            abort(403, 'Invalid signature');
        }

        return $next($request);
    }
}
