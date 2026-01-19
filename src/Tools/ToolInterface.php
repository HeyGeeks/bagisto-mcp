<?php

namespace HeyGeeks\BagistoMCP\Tools;

interface ToolInterface
{
    /**
     * Get the tool name.
     *
     * @return string
     */
    public function name(): string;

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
