<?php

namespace HeyGeeks\BagistoMCP\Tools;

abstract class BaseTool implements ToolInterface
{
    /**
     * Get the full tool definition for MCP discovery.
     *
     * @return array
     */
    public function toDefinition(): array
    {
        return [
            'name' => $this->name(),
            'description' => $this->description(),
            'inputSchema' => $this->inputSchema(),
        ];
    }
}
