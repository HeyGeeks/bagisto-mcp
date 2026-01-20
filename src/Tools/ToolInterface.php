<?php

namespace HeyGeeks\BagistoMCP\Tools;

interface ToolInterface
{
    /**
     * Get the tool name (internal, with dots e.g. "products.list").
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the Claude-compatible tool name (with underscores e.g. "products_list").
     * Claude requires tool names matching: ^[a-zA-Z0-9_-]{1,64}$
     *
     * @return string
     */
    public function claudeName(): string;

    /**
     * Get the tool description for LLM context.
     *
     * @return string
     */
    public function description(): string;

    /**
     * Get the input schema (JSON Schema format).
     *
     * @return array
     */
    public function inputSchema(): array;

    /**
     * Execute the tool logic.
     *
     * @param array $arguments
     * @return array
     */
    public function execute(array $arguments): array;

    /**
     * Get the full tool definition for MCP discovery.
     *
     * @return array
     */
    public function toDefinition(): array;
}
