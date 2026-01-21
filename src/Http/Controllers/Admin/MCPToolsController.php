<?php

namespace HeyGeeks\BagistoMCP\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


use HeyGeeks\BagistoMCP\Models\ToolSetting;
use Mcp\Capability\Attribute\McpTool;
use ReflectionClass;

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

        foreach ($tools as $key => $class) {
            if (class_exists($class)) {
                $reflection = new ReflectionClass($class);

                foreach ($reflection->getMethods() as $method) {
                    $attributes = $method->getAttributes(McpTool::class);

                    if (!empty($attributes)) {
                        $toolName = $key; // specific key from config

                        // Parse DocBlock for description
                        $docComment = $method->getDocComment();
                        $description = '';
                        if ($docComment) {
                            $lines = explode("\n", $docComment);
                            foreach ($lines as $line) {
                                $line = trim($line, " \t*\/");
                                if (!empty($line) && strpos($line, '@') !== 0) {
                                    $description = $line;
                                    break;
                                }
                            }
                        }

                        $isEnabled = ToolSetting::isEnabled($key);

                        $toolsData[] = [
                            'name' => $toolName,
                            'description' => $description,
                            'requires_auth' => $this->requiresAuth($key),
                            'is_enabled' => $isEnabled,
                            'class' => $class,
                            'config' => json_encode(config("mcp.tools_config.{$key}", []), JSON_PRETTY_PRINT),
                        ];

                        // We only support one tool per class for now in this admin view logic
                        // (matches the config structure where 1 key = 1 class)
                        break;
                    }
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
     * Show the form for editing the specified tool.
     *
     * @param  string  $toolName
     * @return \Illuminate\View\View
     */
    public function edit($toolName)
    {
        $tools = config('mcp.tools', []);

        if (!isset($tools[$toolName])) {
            session()->flash('error', trans('mcp::app.admin.tools.tool-not-found'));
            return redirect()->route('admin.mcp.tools.index');
        }

        $toolClass = $tools[$toolName];

        // Handle new Attribute-based tools
        $attributes = (new ReflectionClass($toolClass))->getAttributes(McpTool::class);
        $name = $toolName;
        $description = '';

        if (!empty($attributes)) {
            $instance = $attributes[0]->newInstance();
            $name = $instance->name;
            // Extract description from docblock if possible, otherwise leave empty or use class name
            $description = $name; // Placeholder
        } elseif (app($toolClass) instanceof ToolInterface) {
            // Legacy support
            $definition = app($toolClass)->toDefinition();
            $description = $definition['description'] ?? '';
        }

        $config = ToolSetting::getConfig($toolName);
        $isEnabled = ToolSetting::isEnabled($toolName);

        return view('mcp::admin.mcp.tools.edit', [
            'tool' => [
                'name' => $toolName,
                'class' => $toolClass,
                'description' => $description,
                'config' => json_encode($config ?? [], JSON_PRETTY_PRINT),
                'is_enabled' => $isEnabled,
            ],
        ]);
    }

    /**
     * Update the specified tool in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $toolName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $toolName)
    {
        $tools = config('mcp.tools', []);

        if (!isset($tools[$toolName])) {
            session()->flash('error', trans('mcp::app.admin.tools.tool-not-found'));
            return redirect()->route('admin.mcp.tools.index');
        }

        $data = $request->validate([
            'is_enabled' => 'boolean',
            'config' => 'nullable|json',
        ]);

        $setting = ToolSetting::firstOrCreate(['tool_name' => $toolName]);

        $setting->is_enabled = $request->has('is_enabled');

        if (!empty($data['config'])) {
            $setting->config = json_decode($data['config'], true);
        } else {
            $setting->config = [];
        }

        $setting->save();

        session()->flash('success', trans('admin::app.common.update-success'));

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
