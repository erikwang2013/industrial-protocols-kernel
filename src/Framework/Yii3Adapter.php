<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework;

class Yii3Adapter implements FrameworkAdapterInterface
{
    public function detect(): bool
    {
        return class_exists('Yiisoft\Yii\Runner\ApplicationRunner');
    }

    public function getName(): string
    {
        return 'yii3';
    }

    public function registerConfig(): void
    {
        // Yii3 merges config via yiisoft/config plugin (config/common/*.php)
    }

    public function registerServices(): void
    {
        // Services are registered in the app's DI config (config/common/di.php)
    }

    public function registerCommands(): void
    {
        // Commands are registered in config/common/commands*.php
    }

    public function getConfigPath(): string
    {
        // ponytail: Yii3 has no global app object to read the root from, so CWD is the
        // default. Apps running elsewhere pass the path explicitly to Yii3\KernelFactory.
        return getcwd() . '/config/industrial-protocols.php';
    }

    public function isLongRunning(): bool
    {
        // RoadRunner sets RR_MODE inside the worker; Swoole is detected by extension.
        return getenv('RR_MODE') !== false || class_exists('Swoole\Coroutine');
    }
}
