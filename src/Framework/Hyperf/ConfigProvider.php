<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework\Hyperf;

use Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console\ConnectCommand;
use Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console\GatewayListCommand;
use Erikwang2013\IndustrialProtocols\Kernel;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Kernel::class => static fn (): Kernel => KernelFactory::create(),
            ],
            'commands' => [
                ConnectCommand::class,
                GatewayListCommand::class,
            ],
        ];
    }
}
