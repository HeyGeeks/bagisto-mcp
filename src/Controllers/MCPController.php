<?php

namespace HeyGeeks\BagistoMCP\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use HeyGeeks\BagistoMCP\Tools\ToolInterface;
use HeyGeeks\BagistoMCP\Models\ToolSetting;

class MCPController extends Controller
{
    public function handle(Request $request)
    {
        if (!config('mcp.enabled')) {
            return response()->json(['error' => 'MCP Server is disabled'], 503);
        }

        // If no tool is specified, return the full tool definitions (Discovery Mode)
        if (!$request->has('tool')) {
            return $this->listTools();
        }

        $request->validate([
            'tool' => 'required|string',
            'arguments' => 'array',
        ]);

        $toolName = $request->input('tool');
        $arguments = $request->input('arguments', []);

        try {
            // Resolve tool - supports both dot (products.list) and underscore (products_list) naming
            $result = $this->resolveTool($toolName);

            if (!$result) {
                return response()->json([
                    'error' => "Tool '{$toolName}' not found",
                    'available_tools' => array_keys(config('mcp.tools', [])),
                ], 404);
            }

            [$tool, $normalizedName] = $result;

            // Check if the tool is enabled (use normalized dot notation for config lookup)
            if (!ToolSetting::isEnabled($normalizedName)) {
                return response()->json([
                    'error' => "Tool '{$toolName}' is currently disabled",
                    'code' => 'TOOL_DISABLED',
                ], 403);
            }

            $result = $tool->execute($arguments);

            return response()->json([
                'success' => true,
                'tool' => $toolName,
                'result' => $result,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : [],
            ], 500);
        }
    }

    /**
     * List all available tools with their full definitions.
     * Only lists enabled tools.
     */
    private function listTools()
    {
        $toolClasses = config('mcp.tools', []);
        $definitions = [];

        foreach ($toolClasses as $name => $class) {
            // Only include enabled tools in discovery
            if (!ToolSetting::isEnabled($name)) {
                continue;
            }

            if (class_exists($class)) {
                $tool = app($class);
                if ($tool instanceof ToolInterface) {
                    $definitions[] = $tool->toDefinition();
                }
            }
        }

        return response()->json([
            'server' => 'Bagisto MCP',
            'version' => config('mcp.version', '0.1.0-beta'),
            'status' => config('mcp.status', 'under_development'),
            'tools' => $definitions,
        ]);
    }

    /**
     * Resolve a tool by name.
     * Supports both dot notation (products.list) and underscore notation (products_list).
     *
     * @param string $name Tool name in either format
     * @return array|null Returns [ToolInterface, normalizedName] or null if not found
     */
    private function resolveTool(string $name): ?array
    {
        $tools = config('mcp.tools', []);

        // Try exact match first (dot notation from config)
        if (isset($tools[$name]) && class_exists($tools[$name])) {
            return [app($tools[$name]), $name];
        }

        // Try converting underscore to dot (Claude sends underscore format)
        $dotName = str_replace('_', '.', $name);
        if (isset($tools[$dotName]) && class_exists($tools[$dotName])) {
            return [app($tools[$dotName]), $dotName];
        }

        return null;
    }
}
