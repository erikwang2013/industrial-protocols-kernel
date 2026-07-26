<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Tests\Unit;

use Erikwang2013\IndustrialProtocols\Connection\Strategy\EagerStrategy;
use Erikwang2013\IndustrialProtocols\Protocol\ConnectorInterface;
use PHPUnit\Framework\TestCase;

class EagerStrategyTest extends TestCase
{
    public function testEagerStrategyCallsFactoryOnFirstAccess(): void
    {
        $connector = $this->createMock(ConnectorInterface::class);
        $factoryCalled = false;

        $strategy = new EagerStrategy();
        $result = $strategy->getOrCreate('dev-1', function () use ($connector, &$factoryCalled) {
            $factoryCalled = true;
            return $connector;
        });

        $this->assertTrue($factoryCalled);
        $this->assertSame($connector, $result);
    }

    public function testEagerStrategyReusesExistingConnection(): void
    {
        $connector = $this->createMock(ConnectorInterface::class);

        $strategy = new EagerStrategy();
        $strategy->getOrCreate('dev-1', fn() => $connector);

        $factoryCalled = false;
        $result = $strategy->getOrCreate('dev-1', function () use (&$factoryCalled) {
            $factoryCalled = true;
            return $this->createMock(ConnectorInterface::class);
        });

        $this->assertFalse($factoryCalled);
        $this->assertSame($connector, $result);
    }

    public function testEagerStrategyDisconnect(): void
    {
        $connector = $this->createMock(ConnectorInterface::class);
        $connector->expects($this->once())->method('disconnect');

        $strategy = new EagerStrategy();
        $strategy->getOrCreate('dev-1', fn() => $connector);
        $strategy->disconnect('dev-1');

        $this->assertEmpty($strategy->getActiveConnections());
    }
}
