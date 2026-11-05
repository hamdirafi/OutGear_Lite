<?php

namespace App\Jobs;

use App\Models\Rental;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessRentalNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Rental $rental) {}

    public function handle(): void
    {
        Log::warning("OUTGEAR WORKER: Transaksi sewa #{$this->rental->id} a.n {$this->rental->customer_name} diproses di background.");
    }
}