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
    protected $description = 'Cancel expired pending orders and release seats/quota';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService): int
    {
        $this->info('Cancelling expired orders...');

        $count = $orderService->cancelExpiredOrders();

        $this->info("Cancelled {$count} expired orders");

        return Command::SUCCESS;
    }
}
