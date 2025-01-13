<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class SendSubscriptionToThirdParty implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(array $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = [
            'ProductName' => $this->subscription['name'],
            'Price' => (float)$this->subscription['price'],
            'Timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
        ];
        
        Http::post('https://very-slow-api.com/orders', $payload);
    }
}
