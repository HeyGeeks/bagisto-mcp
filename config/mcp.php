<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MCP Server Status
    |--------------------------------------------------------------------------
    |
    | Enable or disable the MCP server. When disabled, all requests will
    | return a 503 Service Unavailable response.
    |
    */
    'enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Version Information
    |--------------------------------------------------------------------------
    */
    'version' => '0.1.0-beta',
    'status' => 'under_development',

    /*
    |--------------------------------------------------------------------------
    | MCP Endpoint URL
    |--------------------------------------------------------------------------
    |
    | Customize the URL endpoint for the MCP server.
    | Default: 'mcp' (accessible at /mcp)
    | Examples: 'api/mcp', 'ai/mcp', 'llm/tools'
    |
    */
    'endpoint' => env('MCP_ENDPOINT', 'mcp'),

    /*
    |--------------------------------------------------------------------------
    | Authentication Method
    |--------------------------------------------------------------------------
    |
    | The authentication method to use for protected tools.
    | Options: 'sanctum', 'custom'
    |
    */
    'auth' => 'sanctum',

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Maximum number of requests per minute per IP/token.
    |
    */
    'rate_limit' => 60,

    /*
    |--------------------------------------------------------------------------
    | Default Scopes
    |--------------------------------------------------------------------------
    |
    | Default permission scopes for new MCP tokens.
    |
    */
    'default_scopes' => ['products:read', 'categories:read', 'store:read'],

    /*
    |--------------------------------------------------------------------------
    | Available Tools
    |--------------------------------------------------------------------------
    |
    | Register all MCP tools here. Each tool must implement ToolInterface.
    | Tools are organized by category for clarity.
    |
    */
    'tools' => [
        // Product Tools
        'products.list' => \HeyGeeks\BagistoMCP\Tools\ProductListTool::class,
        'products.search' => \HeyGeeks\BagistoMCP\Tools\ProductSearchTool::class,
        'products.detail' => \HeyGeeks\BagistoMCP\Tools\ProductDetailTool::class,

        // Category Tools
        'categories.list' => \HeyGeeks\BagistoMCP\Tools\CategoryListTool::class,

        // Customer Tools (require authentication)
        'customer.login' => \HeyGeeks\BagistoMCP\Tools\CustomerLoginTool::class,
        'customer.profile' => \HeyGeeks\BagistoMCP\Tools\CustomerProfileTool::class,

        // Order Tools
        'orders.status' => \HeyGeeks\BagistoMCP\Tools\OrderStatusTool::class,
        'orders.history' => \HeyGeeks\BagistoMCP\Tools\OrderHistoryTool::class,

        // Cart Tools (read-only)
        'cart.preview' => \HeyGeeks\BagistoMCP\Tools\CartPreviewTool::class,

        // Wishlist Tools
        'wishlist.view' => \HeyGeeks\BagistoMCP\Tools\WishlistTool::class,

        // Store Tools
        'store.info' => \HeyGeeks\BagistoMCP\Tools\StoreInfoTool::class,
    ],
];
