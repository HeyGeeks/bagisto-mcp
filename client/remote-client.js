#!/usr/bin/env node

/**
 * Bagisto MCP Remote Client
 * 
 * This script connects your local MCP client (Claude Desktop, etc.) to a remote Bagisto MCP server
 * via Server-Sent Events (SSE).
 * 
 * Usage:
 *   node remote-client.js <url> [api_token]
 * 
 * Example:
 *   node remote-client.js https://your-store.com/mcp
 */

const { EventSource } = require('eventsource');
const axios = require('axios');

const args = process.argv.slice(2);

if (args.length < 1) {
    console.error('Usage: node remote-client.js <url> [api_token]');
    process.exit(1);
}

const baseUrl = args[0];
const apiToken = args[1]; // Optional Bearer token

const headers = {};
if (apiToken) {
    headers['Authorization'] = `Bearer ${apiToken}`;
}

// 1. Establish SSE Connection (GET)
console.error(`Connecting to ${baseUrl}...`);

const es = new EventSource(baseUrl, { headers });

es.onopen = () => {
    console.error('Connected to SSE stream.');
};

es.onerror = (err) => {
    console.error('SSE Error:', err);
};

// 2. Handle Incoming Messages (Server -> Client)
es.addEventListener('message', (event) => {
    try {
        const data = JSON.parse(event.data);

        // Forward to Stdout (to Claude)
        process.stdout.write(JSON.stringify(data) + "\n");
    } catch (e) {
        console.error('Failed to parse incoming message:', event.data);
    }
});

// 3. Handle Outgoing Messages (Client -> Server)
// Read from Stdin (from Claude) and POST to server
process.stdin.setEncoding('utf8');

let buffer = '';

process.stdin.on('data', (chunk) => {
    buffer += chunk;

    const lines = buffer.split('\n');
    buffer = lines.pop(); // Keep incomplete line in buffer

    for (const line of lines) {
        if (line.trim()) {
            try {
                const message = JSON.parse(line);
                sendMessage(message);
            } catch (e) {
                console.error('Invalid JSON from stdin');
            }
        }
    }
});

async function sendMessage(message) {
    try {
        await axios.post(baseUrl, message, {
            headers: {
                'Content-Type': 'application/json',
                ...headers
            }
        });
    } catch (error) {
        console.error('Failed to send message:', error.message);
    }
}
