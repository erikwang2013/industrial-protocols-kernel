# Industrial Protocols Kernel — Micro-Kernel

> [中文](README.md)

Micro-kernel infrastructure providing connection management, config management, gateway engine, event system, coroutine adaptation, framework adapters, hardware bridge, vendor profiles. Core dependency for all 42 protocol packages.

## Installation

```bash
composer require erikwang2013/industrial-protocols-kernel erikwang2013/industrial-protocols-kernel
```

> Depends on [erikwang2013/industrial-protocols-kernel](https://github.com/erikwang2013/industrial-protocols-kernel) for connection management, protocol registry, coroutine adaptation, event system and more.

## Architecture

Micro-kernel + Protocol SDK. The kernel only defines what a protocol is (6 core interfaces) without containing any protocol implementation. Protocol packages are independently installed with zero coupling, plugging in via SDK interface implementations. The kernel auto-discovers installed protocol packages at boot via ProtocolRegistry (composer.json extra field).

## Features

ProtocolRegistry auto-discovery, ConnectionManager (Lazy/Eager/Pooled strategies+health check+auto-reconnect), ConfigRepository (File/Database/Env), GatewayEngine (poll/change/cron+CircuitBreaker+Transform pipeline), CoroutineAdapter (Swoole→Fiber→Sync), 6 framework adapters (Laravel/Webman/Hyperf/ThinkPHP/Yii2/PlainPHP), BridgeInterface (ExternalProcessBridge+TcpGatewayBridge+BridgeConnector), VendorProfile (12 vendors+VendorBridgeFactory), Event system (PSR-14, 13 types), Log drivers (PsrLogDriver/FileLogDriver/NullLogDriver), Retry strategies (NoRetry/Fixed/ExponentialBackoff+Jitter), Metrics (Counter/Gauge/Histogram+Prometheus), Alert channels (AlertManager+Webhook/Log), Security (InputValidator), Exceptions (20+ types)

## Supported Frameworks

Compatible with 6 PHP runtimes via kernel framework adapters: Laravel (ServiceProvider+Facade+artisan), Webman (config/plugin auto-discovery+ProtocolProcess), Hyperf (ConfigProvider+DI+KernelFactory), ThinkPHP (services.php+IndustrialProtocolsService), Yii2 (Bootstrap+component), Plain PHP (direct Kernel instantiation)

### Laravel

```php
// AppServiceProvider::boot()
$kernel = app(Kernel::class);
$kernel->getProtocolRegistry()->register(new ModbusProtocol());
$kernel->boot();
$conn = $kernel->getConnectionManager()->connect('device-id');
```

### Webman

Auto-boot via ProtocolProcess on worker start. Configure at `config/plugin/erikwang2013/industrial-protocols-kernel/config/industrial-protocols.php`.

### Hyperf

```php
$kernel = \Hyperf\Context\ApplicationContext::getContainer()->get(Kernel::class);
```

## Usage

```php
use Erikwang2013\IndustrialProtocols\Kernel;
use Erikwang2013\IndustrialProtocols\Modbus\ModbusProtocol;

$kernel = new Kernel(['config_path' => __DIR__ . '/config.php']);
$kernel->getProtocolRegistry()->register(new ModbusProtocol());
$kernel->boot();

$conn = $kernel->getConnectionManager()->connect('plc-001');
$result = $conn->read('40001');

$kernel->shutdown();
```

## Configuration

```php
'devices' => [
    'device-id' => [
        'protocol' => 'kernel',
        'host'     => '192.168.1.10',
        'port'     => 0,
        'timeout'  => 3000,
    ],
],
```

## Adapter Vendors

Beckhoff (TwinCAT ADS), Siemens (Open Communication), B&R (Automation Studio/openPOWERLINK), Bosch Rexroth (ctrlX CORE/netX), Hilscher (netX SoC), HMS/Anybus (Communicator/X-gateway), Moxa (MGate), Phoenix Contact (AXL F), Bihl+Wiedemann (AS-i), ifm electronic (IO-Link), Pepperl+Fuchs (AS-i/HART), Softing (FF/PROFIBUS)

## Requirements

- PHP >= 8.1
- Composer
- erikwang2013/industrial-protocols-kernel

## Related Links

- [Industrial Protocols Main Project](https://github.com/erikwang2013/industrial-protocols)
- [Kernel](https://github.com/erikwang2013/industrial-protocols-kernel)
- [All 42 Protocol Packages](https://github.com/erikwang2013/industrial-protocols#supported-protocols)

## License

MIT — Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
