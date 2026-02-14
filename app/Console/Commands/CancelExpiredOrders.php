<?php

namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel pending orders that have exceeded their expiration time';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService)
    {
        $this->info('Checking for expired orders...');

        $count = $orderService->cancelExpiredOrders();

        if ($count > 0) {
            $this->info("Successfully cancelled {$count} expired orders.");
        } else {
            $this->info('No expired orders found.');
        }
    }
}
