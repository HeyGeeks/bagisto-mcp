<?php

namespace HeyGeeks\BagistoMCP;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class MCPServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Load MCP API routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/mcp.php');

        // Load Admin routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/admin.php');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'mcp');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'mcp');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__ . '/../config/mcp.php' => config_path('mcp.php'),
        ], 'mcp-config');

        // Publish assets
        $this->publishes([
            __DIR__ . '/Resources/assets/images' => public_path('vendor/mcp/images'),
        ], 'mcp-assets');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Register MCP config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/mcp.php',
            'mcp'
        );

        // Register admin menu
        $this->mergeConfigFrom(
            __DIR__ . '/Config/menu.php',
            'menu.admin'
        );

        // Register ACL
        $this->mergeConfigFrom(
            __DIR__ . '/Config/acl.php',
            'acl'
        );
    }
}
