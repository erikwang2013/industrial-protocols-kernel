<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework;

class HyperfAdapter implements FrameworkAdapterInterface
{
    public function detect(): bool
    {
        // ApplicationFactory is what Hyperf itself uses to assemble the console
        // application, so it is present in every Hyperf skeleton.
        return class_exists('Hyperf\Framework\ApplicationFactory');
    }

    public function getName(): string
    {
        return 'hyperf';
    }

    public function registerConfig(): void
    {
        // Hyperf merges package config through ConfigProvider (composer extra.hyperf.config)
    }

    public function registerServices(): void
    {
        // Services are registered through the ConfigProvider 'dependencies' key
    }

    public function registerCommands(): void
    {
        // Commands are registered through the ConfigProvider 'commands' key
    }

    public function getConfigPath(): string
    {
        return $this->rootPath() . '/config/autoload/industrial-protocols.php';
    }

    public function isLongRunning(): bool
    {
        return true; // Hyperf always serves from Swoole/Swow workers
    }

    private function rootPath(): string
    {
        return defined('BASE_PATH') ? BASE_PATH : getcwd();
    }
}
