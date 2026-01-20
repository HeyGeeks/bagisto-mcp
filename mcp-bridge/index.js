#!/usr/bin/env node

import { Server } from "@modelcontextprotocol/sdk/server/index.js";
import { StdioServerTransport } from "@modelcontextprotocol/sdk/server/stdio.js";
import {
    CallToolRequestSchema,
    ListToolsRequestSchema,
} from "@modelcontextprotocol/sdk/types.js";
import axios from "axios";

// BAGISTO_MCP_URL is required - no default to force explicit configuration
const MCP_URL = process.env.BAGISTO_MCP_URL;

if (!MCP_URL) {
    process.stderr.write("ERROR: BAGISTO_MCP_URL environment variable is required.\n");
    process.stderr.write("Example: BAGISTO_MCP_URL=https://your-store.com/mcp\n");
    process.exit(1);
}

// Create MCP server
const server = new Server(
    {
        name: "bagisto-mcp-bridge",
        version: "1.0.0",
    },
    {
        capabilities: {
            tools: {},
        },
    }
);

// Fetch tools from Bagisto MCP
async function fetchBagistoTools() {
    try {
        const response = await axios.post(MCP_URL, {}, {
            headers: { "Content-Type": "application/json" },
            timeout: 10000,
        });
        return response.data.tools || [];
    } catch (error) {
        process.stderr.write(`Failed to fetch tools from ${MCP_URL}: ${error.message}\n`);
        return [];
    }
}

// Execute a tool on Bagisto MCP
async function executeBagistoTool(toolName, args) {
    try {
        const response = await axios.post(
            MCP_URL,
            {
                tool: toolName,
                arguments: args,
            },
            {
                headers: { "Content-Type": "application/json" },
                timeout: 30000,
            }
        );
        return response.data;
    } catch (error) {
        return {
            error: true,
            message: error.response?.data?.error || error.message,
        };
    }
}

// Normalize input schema to ensure properties is always an object
function normalizeInputSchema(schema) {
    if (!schema) {
        return { type: "object", properties: {} };
    }

    // Fix: Bagisto may return properties as array [], convert to object {}
    if (Array.isArray(schema.properties)) {
        schema.properties = {};
    }

    return {
        type: schema.type || "object",
        properties: schema.properties || {},
        required: Array.isArray(schema.required) ? schema.required : [],
    };
}

// List available tools
// Note: Tool name mapping is handled by the PHP backend
// Tools are returned with Claude-compatible names (underscores instead of dots)
server.setRequestHandler(ListToolsRequestSchema, async () => {
    const bagistoTools = await fetchBagistoTools();

    const tools = bagistoTools.map((tool) => ({
        name: tool.name,
        description: tool.description || `Bagisto MCP tool: ${tool.name}`,
        inputSchema: normalizeInputSchema(tool.inputSchema),
    }));

    return { tools };
});

// Handle tool calls
// Note: Tool name is passed as-is to Bagisto - PHP handles both naming conventions
server.setRequestHandler(CallToolRequestSchema, async (request) => {
    const { name, arguments: args } = request.params;

    const result = await executeBagistoTool(name, args || {});

    return {
        content: [
            {
                type: "text",
                text: JSON.stringify(result, null, 2),
            },
        ],
    };
});

// Start server
async function main() {
    const transport = new StdioServerTransport();
    await server.connect(transport);
    process.stderr.write(`Bagisto MCP bridge connected to ${MCP_URL}\n`);
}

main().catch((error) => {
    process.stderr.write(`Fatal error: ${error.message}\n`);
    process.exit(1);
});
