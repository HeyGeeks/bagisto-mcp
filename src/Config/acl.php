<?php

return [
    [
        'key' => 'settings.mcp',
        'name' => 'mcp::app.admin.acl.mcp-server',
        'route' => 'admin.mcp.index',
        'sort' => 11,
    ],
    [
        'key' => 'settings.mcp.dashboard',
        'name' => 'mcp::app.admin.acl.dashboard',
        'route' => 'admin.mcp.index',
        'sort' => 1,
    ],
    [
        'key' => 'settings.mcp.tools',
        'name' => 'mcp::app.admin.acl.tools',
        'route' => 'admin.mcp.tools.index',
        'sort' => 2,
    ]
];
