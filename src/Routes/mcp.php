<?php

use Illuminate\Support\Facades\Route;
use HeyGeeks\BagistoMCP\Http\Controllers\MCPController;

$endpoint = config('mcp.endpoint', 'mcp');

Route::group(['middleware' => ['api'], 'prefix' => $endpoint], function () {
    Route::match(['get', 'post', 'options'], '/', [MCPController::class, 'handle'])
        ->name('mcp.endpoint');
});
