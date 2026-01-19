<?php

namespace HeyGeeks\BagistoMCP\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use HeyGeeks\BagistoMCP\Tools\ToolInterface;
use HeyGeeks\BagistoMCP\Models\ToolSetting;

class MCPToolsController extends Controller
{
    /**
     * Display list of all MCP tools.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tools = config('mcp.tools', []);
        $toolsData = [];

        foreach ($tools as $name => $class) {
            if (class_exists($class)) {
                $tool = app($class);
                if ($tool instanceof ToolInterface) {
                    $definition = $tool->toDefinition();
                    $isEnabled = ToolSetting::isEnabled($name);

                    $toolsData[] = [
                        'name' => $name,
                        'description' => $definition['description'] ?? '',
                        'requires_auth' => $this->requiresAuth($name),
                        'is_enabled' => $isEnabled,
                    ];
                }
            }
        }

        return view('mcp::admin.mcp.tools.index', [
            'tools' => $toolsData,
        ]);
    }

    /**
     * Toggle a tool's enabled status.
     *
     * @param  string  $tool
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggle(string $tool)
    {
        $tools = config('mcp.tools', []);

        if (!isset($tools[$tool])) {
            session()->flash('error', trans('mcp::app.admin.tools.tool-not-found'));
            return redirect()->route('admin.mcp.tools.index');
        }

        $isNowEnabled = ToolSetting::toggle($tool);

        $message = $isNowEnabled
            ? trans('mcp::app.admin.tools.enabled-success', ['tool' => $tool])
            : trans('mcp::app.admin.tools.disabled-success', ['tool' => $tool]);

        session()->flash('success', $message);

        return redirect()->route('admin.mcp.tools.index');
    }

    /**
     * Check if a tool requires authentication.
     *
     * @param  string  $toolName
     * @return bool
     */
    private function requiresAuth(string $toolName): bool
    {
        $authTools = [
            'customer.profile',
            'orders.history',
            'wishlist.view',
        ];

        return in_array($toolName, $authTools);
    }
}
