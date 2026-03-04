<?php

namespace App\Listeners;

use App\Events\OrderConfirmed;
use App\Jobs\ExportOrderJob;

class DispatchOrderExport
{
    /**
     * Handle the event.
     */
    public function handle(OrderConfirmed $event): void
    {
        ExportOrderJob::dispatch($event->orderId);
    }
}
