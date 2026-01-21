<?php

namespace HeyGeeks\BagistoMCP\Services;

use Illuminate\Support\Facades\App;
use Mcp\Server;
use HeyGeeks\BagistoMCP\Models\ToolSetting;
use Mcp\Capability\Attribute\McpTool;
use ReflectionClass;

class MCPServerFactory
{
    public static function create(): Server
    {
        // specific container adapter for MCP
        $container = new class (app()) implements \Psr\Container\ContainerInterface {
            private $app;
            public function __construct($app)
            {
                $this->app = $app;
            }
            public function get(string $id)
            {
                return $this->app->make($id);
            }
            public function has(string $id): bool
            {
                return true;
            }
        };

        $builder = Server::builder()
            ->setServerInfo('Bagisto MCP', config('mcp.version', '1.0.0'))
            ->setContainer($container);

        // Register tools
        $tools = config('mcp.tools', []);
        foreach ($tools as $keyName => $toolClass) {
            if (class_exists($toolClass) && ToolSetting::isEnabled($keyName)) {
                try {
                    $instance = $container->get($toolClass);
                    $reflection = new ReflectionClass($instance);

                    foreach ($reflection->getMethods() as $method) {
                        $attrs = $method->getAttributes(McpTool::class);
                        if (!empty($attrs)) {
                            // Register using [ClassName, methodName] array
                            $builder->addTool([$toolClass, $method->getName()]);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }
        }

        return $builder->build();
    }
}
