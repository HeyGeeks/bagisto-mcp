<?php

use Illuminate\Support\Facades\Route;
use HeyGeeks\BagistoMCP\Http\Controllers\Admin\MCPDashboardController;
use HeyGeeks\BagistoMCP\Http\Controllers\Admin\MCPToolsController;
use HeyGeeks\BagistoMCP\Http\Controllers\Admin\MCPSettingsController;

Route::group([
    'middleware' => ['web', 'admin'],
    'prefix' => config('app.admin_url') . '/mcp',
], function () {
    /**
     * MCP Dashboard
     */
    Route::get('/', [MCPDashboardController::class, 'index'])->name('admin.mcp.index');

    /**
     * MCP Tools Management
     */
    Route::prefix('tools')->group(function () {
        Route::get('/', [MCPToolsController::class, 'index'])->name('admin.mcp.tools.index');
        Route::get('/{tool}/edit', [MCPToolsController::class, 'edit'])->name('admin.mcp.tools.edit');
        Route::put('/{tool}', [MCPToolsController::class, 'update'])->name('admin.mcp.tools.update');
        Route::post('/{tool}/toggle', [MCPToolsController::class, 'toggle'])->name('admin.mcp.tools.toggle');
    });
});
