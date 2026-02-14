<?php

namespace App\Jobs;

use App\Models\WebhookLog;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMidtransWebhook implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [30, 60, 120, 300, 600];

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WebhookLog $webhookLog
    ) {}

    /**
     * Execute the job.
     */
    public function handle(PaymentService $paymentService): void
    {
        Log::info("Processing webhook log ID: {$this->webhookLog->id}");

        try {
            $paymentService->handleCallback($this->webhookLog->payload);

            $this->webhookLog->update([
                'status' => 'processed',
                'response' => 'Successfully processed',
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to process webhook log ID: {$this->webhookLog->id}. Error: " . $e->getMessage());

            $this->webhookLog->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);

            throw $e; // Trigger retry
        }
    }
}
