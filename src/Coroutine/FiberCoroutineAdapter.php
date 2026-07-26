<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Coroutine;

use Fiber;

class FiberCoroutineAdapter implements CoroutineAdapterInterface
{
    public function isAvailable(): bool { return PHP_VERSION_ID >= 80100; }
    public function getName(): string { return 'fiber'; }
    public function create(callable $fn): mixed
    {
        $fiber = new Fiber($fn);
        return $fiber->start();
    }
    public function sleep(float $seconds): void
    {
        usleep((int)($seconds * 1_000_000));
    }
    public function parallel(array $callables): array
    {
        $results = [];
        foreach ($callables as $index => $callable) {
            $results[$index] = $callable();
        }
        return array_values($results);
    }
}
