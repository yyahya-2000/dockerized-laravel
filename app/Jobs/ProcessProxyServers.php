<?php

namespace App\Jobs;

use App\Services\ProxyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProxyServers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly array $proxyServers)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(ProxyService $proxyService): void
    {
        $proxyService->checkProxyServers($this->proxyServers);
    }
}
