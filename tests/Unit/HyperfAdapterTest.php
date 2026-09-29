<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Tests\Unit;

use Erikwang2013\IndustrialProtocols\Framework\Hyperf\ConfigProvider;
use Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console\ConnectCommand;
use Erikwang2013\IndustrialProtocols\Framework\Hyperf\Console\GatewayListCommand;
use Erikwang2013\IndustrialProtocols\Framework\Hyperf\KernelFactory;
use Erikwang2013\IndustrialProtocols\Framework\HyperfAdapter;
use Erikwang2013\IndustrialProtocols\Kernel;
use PHPUnit\Framework\TestCase;

class HyperfAdapterTest extends TestCase
{
    public function testHyperfAdapterName(): void
    {
        $adapter = new HyperfAdapter();
        $this->assertSame('hyperf', $adapter->getName());
    }

    public function testHyperfAdapterDetectReturnsFalseWithoutHyperf(): void
    {
        $adapter = new HyperfAdapter();
        $this->assertFalse($adapter->detect());
    }

    public function testHyperfAdapterIsAlwaysLongRunning(): void
    {
        $adapter = new HyperfAdapter();
        $this->assertTrue($adapter->isLongRunning());
    }

    public function testHyperfAdapterConfigPathLivesUnderAutoloadDir(): void
    {
        $adapter = new HyperfAdapter();
        $this->assertStringEndsWith('/config/autoload/industrial-protocols.php', $adapter->getConfigPath());
    }

    public function testConfigProviderRegistersKernelAndCommands(): void
    {
        $config = (new ConfigProvider())();

        $this->assertArrayHasKey(Kernel::class, $config['dependencies']);
        $this->assertIsCallable($config['dependencies'][Kernel::class]);
        $this->assertSame(
            [ConnectCommand::class, GatewayListCommand::class],
            $config['commands'],
        );
    }

    public function testConfigProviderDependencyBootsKernel(): void
    {
        $config = (new ConfigProvider())();
        $kernel = $config['dependencies'][Kernel::class]();

        $this->assertInstanceOf(Kernel::class, $kernel);
        $this->assertSame([], $kernel->getConfigRepository()->getAllDeviceConfigs());

        $kernel->shutdown();
    }

    public function testKernelFactoryBootsKernelFromExplicitPaths(): void
    {
        $configPath = sys_get_temp_dir() . '/hyperf-' . uniqid() . '.php';
        file_put_contents($configPath, '<?php return ' . var_export([
            'devices' => ['plc-001' => ['protocol' => 'modbus', 'host' => '127.0.0.1']],
            'gateway' => ['rules' => []],
            'health_check_interval' => 30,
        ], true) . ';');

        try {
            $kernel = KernelFactory::create($configPath, sys_get_temp_dir() . '/missing-installed.json');

            $this->assertInstanceOf(Kernel::class, $kernel);
            $this->assertArrayHasKey('plc-001', $kernel->getConfigRepository()->getAllDeviceConfigs());

            $kernel->shutdown();
        } finally {
            if (file_exists($configPath)) unlink($configPath);
        }
    }
}
