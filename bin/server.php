#!/usr/bin/env php
<?php

// Suppress deprecation warnings
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', '0');

// Bootstrap Laravel
require_once __DIR__ . '/../../../../vendor/autoload.php';

// Load package dependencies (including mcp/sdk)
$packageAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($packageAutoload)) {
    require_once $packageAutoload;
}

$app = require_once __DIR__ . '/../../../../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Mcp\Server;
use Mcp\Server\Transport\StdioTransport;

// Create MCP Server
// Create a PSR-11 Container adapter for Laravel
// This ensures that has() returns true so the SDK uses make() for instantiation, allowing DI.
$container = new class ($app) implements \Psr\Container\ContainerInterface {
    private $app;
    public function __construct($app)
    {
        $this->app = $app;
    }
    public function get(string $id)
    {
        return $this->app->make($id);
    }
    public function has(string $id): bool
    {
        return true;
    } // Always try to resolve
};

// Create MCP Server
$server = Server::builder()
    ->setServerInfo('Bagisto MCP', '1.0.0')
    ->setContainer($container)
    ->setDiscovery(__DIR__ . '/../src/Tools', ['.'])
    ->build();

$transport = new StdioTransport();
$server->run($transport);
