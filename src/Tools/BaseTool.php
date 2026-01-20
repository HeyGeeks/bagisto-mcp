<?php

namespace HeyGeeks\BagistoMCP\Tools;

abstract class BaseTool implements ToolInterface
{
    /**
     * Get the Claude-compatible tool name (with underscores instead of dots).
     * Claude requires tool names matching: ^[a-zA-Z0-9_-]{1,64}$
     *
     * @return string
     */
    public function claudeName(): string
    {
        return str_replace('.', '_', $this->name());
    }

    /**
     * Get the full tool definition for MCP discovery.
     * Uses Claude-compatible naming for LLM compatibility.
     *
     * @return array
     */
    public function toDefinition(): array
    {
        return [
            'name' => $this->claudeName(),
            'description' => $this->description(),
            'inputSchema' => $this->inputSchema(),
        ];
    }
}
