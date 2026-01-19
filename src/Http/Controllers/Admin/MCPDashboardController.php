<?php

namespace HeyGeeks\BagistoMCP\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use HeyGeeks\BagistoMCP\Tools\ToolInterface;

class MCPDashboardController extends Controller
{
    /**
     * Display the MCP dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tools = config('mcp.tools', []);
        $enabledCount = 0;
        $toolsData = [];

        foreach ($tools as $name => $class) {
            if (class_exists($class)) {
                $tool = app($class);
                if ($tool instanceof ToolInterface) {
                    $definition = $tool->toDefinition();
                    $toolsData[] = [
                        'name' => $name,
                        'description' => $definition['description'] ?? '',
                    ];
                    $enabledCount++;
                }
            }
        }

        return view('mcp::admin.mcp.index', [
            'serverEnabled' => config('mcp.enabled', true),
            'version' => config('mcp.version', '0.1.0-beta'),
            'status' => config('mcp.status', 'under_development'),
            'endpoint' => '/' . config('mcp.endpoint', 'mcp'),
            'totalTools' => count($tools),
            'enabledTools' => $enabledCount,
            'rateLimit' => config('mcp.rate_limit', 60),
        ]);
    }
}
