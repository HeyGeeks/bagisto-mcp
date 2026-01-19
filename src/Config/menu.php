<?php

return [
    /**
     * MCP Server - Settings submenu
     */
    [
        'key' => 'settings.mcp',
        'name' => 'mcp::app.admin.menu.mcp-server',
        'route' => 'admin.mcp.index',
        'sort' => 11,
        'icon' => 'icon-attribute',
    ],
    [
        'key' => 'settings.mcp.dashboard',
        'name' => 'mcp::app.admin.menu.dashboard',
        'route' => 'admin.mcp.index',
        'sort' => 1,
        'icon' => '',
    ],
    [
        'key' => 'settings.mcp.tools',
        'name' => 'mcp::app.admin.menu.tools',
        'route' => 'admin.mcp.tools.index',
        'sort' => 2,
        'icon' => '',
    ],
    [
        'key' => 'settings.mcp.settings',
        'name' => 'mcp::app.admin.menu.settings',
        'route' => 'admin.mcp.settings.index',
        'sort' => 3,
        'icon' => '',
    ],
];
