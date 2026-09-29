<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console;

use Erikwang2013\IndustrialProtocols\Kernel;
use Hyperf\Command\Command;
use Hyperf\Context\ApplicationContext;

class GatewayListCommand extends Command
{
    protected ?string $signature = 'industrial:gateway:list';
    protected string $description = 'List all gateway rules';

    public function handle(): int
    {
        $kernel = ApplicationContext::getContainer()->get(Kernel::class);
        $rules = $kernel->getConfigRepository()->getGatewayRules();

        if (empty($rules)) {
            $this->info('No gateway rules configured.');
            return 0;
        }

        $this->table(['ID', 'Source Device', 'Source Point', 'Target Device', 'Target Point'], array_map(
            fn($r) => [$r['id'] ?? '?', $r['source_device'] ?? '?', $r['source_point'] ?? '?', $r['target_device'] ?? '?', $r['target_point'] ?? '?'],
            $rules,
        ));

        return 0;
    }
}
