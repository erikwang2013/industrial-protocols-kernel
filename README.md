# Industrial Protocols Kernel — 微内核

> [English](README.en.md)

微内核基础设施，提供连接管理、配置管理、网关引擎、事件系统、协程适配、框架适配、硬件桥接、厂商配置。42 个协议包的核心依赖。

## 安装

```bash
composer require erikwang2013/industrial-protocols-kernel erikwang2013/industrial-protocols-kernel
```

> 本包依赖 [erikwang2013/industrial-protocols-kernel](https://github.com/erikwang2013/industrial-protocols-kernel)，内核提供连接管理、协议注册、协程适配、事件系统等基础设施。

## 架构

微内核 + 协议 SDK。内核只定义协议是什么（6 个核心接口），不包含任何具体协议实现。协议包独立安装、零耦合，通过实现 SDK 接口接入内核。内核启动时通过 ProtocolRegistry 自动发现已安装的协议包（composer.json extra 字段）。

## 功能

ProtocolRegistry 协议自动发现与注册、ConnectionManager 连接管理（Lazy/Eager/Pooled 三种策略+健康检查+自动重连）、ConfigRepository 配置管理（File/Database/Env 三种实现）、GatewayEngine 网关引擎（poll/change/cron 触发+CircuitBreaker 熔断器+Transform 变换管道）、CoroutineAdapter 协程适配（Swoole→Fiber→Sync 三级降级）、6 框架适配器（Laravel/Webman/Hyperf/ThinkPHP/Yii2/PlainPHP）、BridgeInterface 硬件桥接（ExternalProcessBridge+TcpGatewayBridge+BridgeConnector）、VendorProfile 厂商预置配置（12家+VendorBridgeFactory）、Event 事件系统（PSR-14 标准，13种事件）、Log 日志驱动（PsrLogDriver/FileLogDriver/NullLogDriver）、Retry 重试策略（NoRetry/FixedRetry/ExponentialBackoff+Jitter）、Metrics 指标采集（Counter/Gauge/Histogram+Prometheus导出）、Alert 告警通道（AlertManager+Webhook/Log）、Security 安全校验（InputValidator 输入校验）、Exception 异常体系（20+分层异常）

## 支持的框架

本包通过内核的框架适配器兼容以下 6 种 PHP 运行时环境：Laravel (ServiceProvider+Facade+artisan)、Webman (config/plugin 自动发现+ProtocolProcess)、Hyperf (ConfigProvider+DI+KernelFactory)、ThinkPHP (services.php+IndustrialProtocolsService)、Yii2 (Bootstrap+组件注册)、Plain PHP (直接实例化 Kernel)

### Laravel 示例

```php
use Erikwang2013\IndustrialProtocols\Kernel;
use Erikwang2013\IndustrialProtocols\Modbus\ModbusProtocol;

// AppServiceProvider::boot()
$kernel = app(Kernel::class);
$kernel->getProtocolRegistry()->register(new ModbusProtocol());
$kernel->boot();

$conn = $kernel->getConnectionManager()->connect('device-id');
$result = $conn->read('address');

// 或使用 Facade
\Erikwang2013\IndustrialProtocols\Framework\Laravel\IndustrialProtocolsFacade::connect('device-id')->read('address');
```

### Webman 示例

Worker 启动时 ProtocolProcess 自动初始化。配置 `config/plugin/erikwang2013/industrial-protocols-kernel/config/industrial-protocols.php`。

### Hyperf 示例

```php
$kernel = \Hyperf\Context\ApplicationContext::getContainer()->get(Kernel::class);
$conn = $kernel->getConnectionManager()->connect('device-id');
```

## 使用说明

```php
use Erikwang2013\IndustrialProtocols\Kernel;
use Erikwang2013\IndustrialProtocols\Modbus\ModbusProtocol;

$kernel = new Kernel(['config_path' => __DIR__ . '/config.php']);
$kernel->getProtocolRegistry()->register(new ModbusProtocol());
$kernel->boot();

// 连接设备
$conn = $kernel->getConnectionManager()->connect('plc-001');
$result = $conn->read('40001');

// 网关引擎
$engine = new GatewayEngine($kernel->getConnectionManager(), ...);
$engine->addRule(new GatewayRule(id: 'rule-1', ...));

// 厂商桥接
$bridge = $kernel->getVendorBridgeFactory()->create('beckhoff', 'CX2030', '3.1');

$kernel->shutdown();
```

## 配置示例

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

## 适配厂商

Beckhoff (TwinCAT ADS)、Siemens (Open Communication)、B&R (Automation Studio/openPOWERLINK)、Bosch Rexroth (ctrlX CORE/netX)、Hilscher (netX SoC)、HMS/Anybus (Communicator/X-gateway)、Moxa (MGate)、Phoenix Contact (AXL F)、Bihl+Wiedemann (AS-i)、ifm electronic (IO-Link)、Pepperl+Fuchs (AS-i/HART)、Softing (FF/PROFIBUS)

## 系统要求

- PHP >= 8.1
- Composer
- erikwang2013/industrial-protocols-kernel

## 相关链接

- [Industrial Protocols 主项目](https://github.com/erikwang2013/industrial-protocols)
- [Kernel 内核](https://github.com/erikwang2013/industrial-protocols-kernel)
- [全部 42 个协议包](https://github.com/erikwang2013/industrial-protocols#支持的协议)

## License

MIT — Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
