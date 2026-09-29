<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Tests\Unit;

use Erikwang2013\IndustrialProtocols\Framework\Yii3Adapter;
use Erikwang2013\IndustrialProtocols\Framework\Yii3\KernelFactory;
use Erikwang2013\IndustrialProtocols\Kernel;
use PHPUnit\Framework\TestCase;

class Yii3AdapterTest extends TestCase
{
    public function testYii3AdapterName(): void
    {
        $adapter = new Yii3Adapter();
        $this->assertSame('yii3', $adapter->getName());
    }

    public function testYii3AdapterDetectReturnsFalseWithoutYii3(): void
    {
        $adapter = new Yii3Adapter();
        $this->assertFalse($adapter->detect());
    }

    public function testYii3AdapterIsLongRunningFollowsRoadRunnerOrSwoole(): void
    {
        $adapter = new Yii3Adapter();
        $expected = getenv('RR_MODE') !== false || class_exists('Swoole\Coroutine');
        $this->assertSame($expected, $adapter->isLongRunning());
    }

    public function testYii3AdapterConfigPathLivesUnderConfigDir(): void
    {
        $adapter = new Yii3Adapter();
        $this->assertStringEndsWith('/config/industrial-protocols.php', $adapter->getConfigPath());
    }

    public function testKernelFactoryBootsKernelFromExplicitPaths(): void
    {
        $configPath = sys_get_temp_dir() . '/yii3-' . uniqid() . '.php';
        file_put_contents($configPath, '<?php return ' . var_export([
            'devices' => [],
            'gateway' => ['rules' => []],
            'health_check_interval' => 30,
        ], true) . ';');

        try {
            $kernel = KernelFactory::create($configPath, sys_get_temp_dir() . '/missing-installed.json');

            $this->assertInstanceOf(Kernel::class, $kernel);
            $this->assertSame([], $kernel->getConfigRepository()->getAllDeviceConfigs());

            $kernel->shutdown();
        } finally {
            if (file_exists($configPath)) unlink($configPath);
        }
    }
}
