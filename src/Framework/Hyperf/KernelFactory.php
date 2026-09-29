<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework\Hyperf;

use Erikwang2013\IndustrialProtocols\Kernel;

/**
 * Boots the Kernel for a Hyperf application.
 *
 * Hyperf resolves `Kernel::class` through {@see ConfigProvider}, which composer registers
 * automatically from `extra.hyperf.config`:
 *
 *     $kernel = ApplicationContext::getContainer()->get(Kernel::class);
 *
 * The default config lives at `config/autoload/industrial-protocols.php`, the directory
 * Hyperf merges into its own config tree.
 */
final class KernelFactory
{
    public static function create(?string $configPath = null, ?string $installedJson = null): Kernel
    {
        $root = defined('BASE_PATH') ? BASE_PATH : getcwd();

        $configPath ??= $root . '/config/autoload/industrial-protocols.php';
        if (!is_file($configPath)) {
            // Nothing published yet — fall back to the kernel's bundled defaults so a
            // fresh `composer require` still boots.
            $configPath = dirname(__DIR__, 3) . '/config/industrial-protocols.php';
        }

        $kernel = new Kernel(['config_path' => $configPath]);
        $kernel->getProtocolRegistry()->autoDiscover(
            $installedJson ?? $root . '/vendor/composer/installed.json'
        );
        $kernel->boot();

        return $kernel;
    }
}
