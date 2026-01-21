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

// Check if MCP is enabled
if (!config('mcp.enabled', true)) {
    fwrite(STDERR, "MCP server is disabled in configuration\n");
    exit(1);
}

use HeyGeeks\BagistoMCP\Services\MCPServerFactory;
use Mcp\Server\Transport\StdioTransport;

$server = MCPServerFactory::create();

$transport = new StdioTransport();
$server->run($transport);
