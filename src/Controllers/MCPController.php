<?php

namespace HeyGeeks\BagistoMCP\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use HeyGeeks\BagistoMCP\Tools\ToolInterface;

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
            $tool = $this->resolveTool($toolName);

            if (!$tool) {
                return response()->json([
                    'error' => "Tool '{$toolName}' not found",
                    'available_tools' => array_keys(config('mcp.tools', [])),
                ], 404);
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
     */
    private function listTools()
    {
        $toolClasses = config('mcp.tools', []);
        $definitions = [];

        foreach ($toolClasses as $name => $class) {
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

    private function resolveTool(string $name): ?ToolInterface
    {
        $tools = config('mcp.tools', []);

        if (isset($tools[$name]) && class_exists($tools[$name])) {
            $class = $tools[$name];
            return app($class);
        }

        return null;
    }
}
