<?php

use Illuminate\Support\Facades\Route;
use HeyGeeks\BagistoMCP\Controllers\MCPController;

$endpoint = config('mcp.endpoint', 'mcp');

Route::group(['middleware' => ['api'], 'prefix' => $endpoint], function () {
    Route::post('/', [MCPController::class, 'handle']);
    Route::get('/', function () {
        $endpoint = config('mcp.endpoint', 'mcp');
        return response()->json([
            'server' => 'Bagisto MCP',
            'status' => config('mcp.status', 'under_development'),
            'version' => config('mcp.version', '0.1.0-beta'),
            'endpoint' => '/' . $endpoint,
            'usage' => [
                'discovery' => "POST /{$endpoint} (without tool parameter)",
                'execute' => "POST /{$endpoint} with {\"tool\": \"tool.name\", \"arguments\": {}}"
            ]
        ]);
    });
});
