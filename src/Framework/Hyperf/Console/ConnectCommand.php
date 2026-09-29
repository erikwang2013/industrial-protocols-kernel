<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console;

use Erikwang2013\IndustrialProtocols\Kernel;
use Hyperf\Command\Command;
use Hyperf\Context\ApplicationContext;

class ConnectCommand extends Command
{
    protected ?string $signature = 'industrial:connect {device : Device ID from config}';
    protected string $description = 'Connect to an industrial device and verify health';

    public function handle(): int
    {
        $deviceId = $this->argument('device');

        try {
            $kernel = ApplicationContext::getContainer()->get(Kernel::class);
            $conn = $kernel->getConnectionManager()->connect($deviceId);
            $health = $kernel->getConnectionManager()->health($deviceId);

            $this->info("Connected to $deviceId");
            $this->table(['State', 'Latency (ms)', 'Last Error'], [
                [$health->state->value, $health->latencyMs, $health->lastError ?? 'N/A'],
            ]);

            return 0;
        } catch (\Throwable $e) {
            $this->error("Failed to connect to $deviceId: " . $e->getMessage());
            return 1;
        }
    }
}
