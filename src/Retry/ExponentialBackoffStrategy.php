<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Retry;

class ExponentialBackoffStrategy implements RetryStrategyInterface
{
    /**
     * @param int $maxRetries Maximum retry attempts (default 3). Total tries = 1 initial + N retries.
     */
    public function __construct(
        private int $maxRetries = 3,
        private int $baseDelayMs = 1000,
        private bool $jitter = false,
        private array $retryableExceptions = [\Throwable::class],
    ) {}
    public function shouldRetry(int $attempt, \Throwable $error): bool
    {
        if ($attempt > $this->maxRetries) return false;
        foreach ($this->retryableExceptions as $class) {
            if ($error instanceof $class) return true;
        }
        return false;
    }
    public function getDelay(int $attempt): int
    {
        $delay = $this->baseDelayMs * (1 << ($attempt - 1));
        if ($this->jitter) {
            $delay = random_int((int)($delay * 0.5), (int)($delay * 1.5));
        }
        return $delay;
    }
}
