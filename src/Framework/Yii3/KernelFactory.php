<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Framework\Yii3;

use Erikwang2013\IndustrialProtocols\Kernel;

/**
 * Yii3 has no global application object, so the Kernel is built through the DI container.
 *
 * Register it in the application's `config/common/di.php`:
 *
 *     return [
 *         Kernel::class => [KernelFactory::class, 'create'],
 *     ];
 *
 * `$container->get(Kernel::class)` then returns the booted kernel — Yii3 DI is shared by
 * default, so it is a singleton. Both arguments are optional because Yii3 resolves
 * callable-definition parameters by type and falls back to defaults for builtins.
 */
final class KernelFactory
{
    /**
     * Boots a kernel relative to the application root (the working directory by default).
     *
     * Pass explicit paths when that root is not the working directory, e.g. for a Yii3
     * app served from `public/`:
     *
     *     Kernel::class => static fn () => KernelFactory::create(dirname(__DIR__, 2) . '/config/industrial-protocols.php'),
     */
    public static function create(?string $configPath = null, ?string $installedJson = null): Kernel
    {
        $root = getcwd();

        $configPath ??= $root . '/config/industrial-protocols.php';
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
