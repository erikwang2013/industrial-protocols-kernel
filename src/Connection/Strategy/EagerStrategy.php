<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Connection\Strategy;

use Erikwang2013\IndustrialProtocols\Protocol\ConnectorInterface;

class EagerStrategy implements StrategyInterface
{
    /** @var array<string, ConnectorInterface> */
    private array $connections = [];

    /** @var array<string, bool> */
    private array $preloaded = [];

    /**
     * @param array<int, array{deviceId: string, factory: callable}> $preload
     */
    public function __construct(private array $preload = [])
    {
        foreach ($preload as $entry) {
            $deviceId = $entry['deviceId'];
            $this->connections[$deviceId] = $entry['factory']();
            $this->preloaded[$deviceId] = true;
        }
    }

    public function getOrCreate(string $deviceId, callable $factory): ConnectorInterface
    {
        if (!isset($this->connections[$deviceId])) {
            $this->connections[$deviceId] = $factory();
        }
        return $this->connections[$deviceId];
    }

    public function disconnect(string $deviceId): void
    {
        if (isset($this->connections[$deviceId])) {
            $this->connections[$deviceId]->disconnect();
            unset($this->connections[$deviceId], $this->preloaded[$deviceId]);
        }
    }

    public function disconnectAll(): void
    {
        foreach ($this->connections as $id => $connector) {
            $connector->disconnect();
            unset($this->connections[$id]);
        }
        $this->preloaded = [];
    }

    public function getActiveConnections(): array
    {
        return $this->connections;
    }
}
