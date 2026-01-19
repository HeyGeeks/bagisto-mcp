# Bagisto MCP Server

> ⚠️ **BETA VERSION - UNDER DEVELOPMENT**
>
> This package is currently in beta. APIs may change without notice.
> Not recommended for production use without thorough testing.

A Laravel package that exposes Bagisto e-commerce capabilities to LLMs (Large Language Models) via the **Model Context Protocol (MCP)**. This enables AI agents to safely interact with your Bagisto store for product discovery, customer authentication, order tracking, and more.

---

## 📋 Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Available Tools](#available-tools)
- [Usage Examples](#usage-examples)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)

---

## Installation

### 1. Install via Composer

The package is available on Packagist:
```bash
composer require heygeeks/bagisto-mcp
```

For local development (via path repository):
```bash
# Already configured in your composer.json as a path repository
composer update heygeeks/bagisto-mcp
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="HeyGeeks\BagistoMCP\MCPServiceProvider"
```

### 3. Clear Caches

```bash
php artisan route:clear
php artisan config:clear
```

---

## Configuration

The configuration file is published to `config/mcp.php`:

```php
return [
    'enabled' => true,           // Enable/disable the MCP server
    'version' => '0.1.0-beta',   // Current version
    'status' => 'under_development',
    'endpoint' => 'mcp',         // Custom MCP endpoint URL
    'auth' => 'sanctum',         // Authentication method
    'rate_limit' => 60,          // Requests per minute
    'default_scopes' => ['products:read', 'categories:read', 'store:read'],
    'tools' => [
        // All registered tools
    ],
];
```

### Custom Endpoint URL

You can customize the MCP endpoint URL in two ways:

**Option 1: Via config file** (`config/mcp.php`)
```php
'endpoint' => 'api/ai/mcp',  // Accessible at /api/ai/mcp
```

**Option 2: Via environment variable** (`.env`)
```env
MCP_ENDPOINT=api/mcp
```

Examples:
- Default: `/mcp`
- Custom: `/api/mcp`, `/ai/tools`, `/llm/mcp`

---

## Available Tools

The MCP server provides **11 tools** organized by category:

### 🛍️ Product Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `products.list` | List products with filtering (category, price, pagination) | ❌ |
| `products.search` | Full-text search for products | ❌ |
| `products.detail` | Get detailed product info by ID or SKU | ❌ |

### 📂 Category Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `categories.list` | List all product categories | ❌ |

### 👤 Customer Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `customer.login` | Authenticate and get access token | ❌ |
| `customer.profile` | Get customer profile information | ✅ |

### 📦 Order Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `orders.status` | Check order status by ID | ⚠️ Optional |
| `orders.history` | List customer's order history | ✅ |

### 🛒 Cart Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `cart.preview` | View current cart contents (read-only) | ❌ |

### ❤️ Wishlist Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `wishlist.view` | View customer's wishlist items | ✅ |

### 🏪 Store Tools

| Tool | Description | Auth Required |
|------|-------------|---------------|
| `store.info` | Get store info, currencies, contact | ❌ |

---

## Usage Examples

### Endpoint

```
POST /mcp
Content-Type: application/json
```

### Discovery (List All Tools)

Send an empty POST request to get all available tools with their schemas:

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json"
```

**Response:**
```json
{
  "server": "Bagisto MCP",
  "version": "0.1.0-beta",
  "status": "under_development",
  "tools": [
    {
      "name": "products.search",
      "description": "Search for products...",
      "inputSchema": {
        "type": "object",
        "properties": {
          "query": {
            "type": "string",
            "description": "Search query string"
          }
        },
        "required": ["query"]
      }
    }
  ]
}
```

### Search Products

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "tool": "products.search",
    "arguments": {
      "query": "running shoes"
    }
  }'
```

### Get Product Details

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "tool": "products.detail",
    "arguments": {
      "sku": "SHOE-001"
    }
  }'
```

### List Categories

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "tool": "categories.list",
    "arguments": {}
  }'
```

### Customer Login

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "tool": "customer.login",
    "arguments": {
      "email": "customer@example.com",
      "password": "secret123"
    }
  }'
```

**Response:**
```json
{
  "success": true,
  "tool": "customer.login",
  "result": {
    "authenticated": true,
    "customer_id": 5,
    "token": "1|abc123xyz...",
    "token_type": "Bearer"
  }
}
```

### Get Order History (Authenticated)

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "tool": "orders.history",
    "arguments": {
      "limit": 5
    }
  }'
```

### Get Store Info

```bash
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{
    "tool": "store.info",
    "arguments": {}
  }'
```

---

## Testing

### Run Package Tests

```bash
php artisan test packages/heygeeks/bagisto-mcp/tests/Feature/MCPTest.php
```

### Manual Testing with cURL

```bash
# Test if MCP server is running
curl http://localhost:8000/mcp

# Test tool discovery
curl -X POST http://localhost:8000/mcp

# Test a specific tool
curl -X POST http://localhost:8000/mcp \
  -H "Content-Type: application/json" \
  -d '{"tool": "store.info", "arguments": {}}'
```

### Verify Route Registration

```bash
php artisan route:list --path=mcp
```

Expected output:
```
GET|HEAD   mcp
POST       mcp
```

---

## Security

### Best Practices

1. **Rate Limiting**: The server enforces 60 requests/minute by default
2. **Read-Only by Default**: Most tools are read-only
3. **Token Authentication**: Sensitive operations require Sanctum tokens
4. **No Direct DB Access**: LLMs cannot directly access the database
5. **Input Validation**: All inputs are validated before processing

### Token Scopes

Tokens can be restricted to specific scopes:
- `products:read` - Access product tools
- `categories:read` - Access category tools
- `customer:read` - Access customer profile
- `orders:read` - Access order information
- `store:read` - Access store information

### Recommendations

- Always use HTTPS in production
- Implement additional rate limiting at the reverse proxy level
- Monitor MCP usage logs
- Regularly rotate tokens

---

## LLM Integration

### For AI Developers

When integrating with your LLM system:

1. **Fetch Tool Definitions**: Call `POST /mcp` without a tool to get all schemas
2. **Register Tools**: Use the returned `inputSchema` for function calling
3. **Execute Tools**: Send tool name and arguments to execute
4. **Handle Auth**: Store tokens and pass them in `Authorization` header

### Example System Prompt

```
You have access to a Bagisto e-commerce store via MCP tools.
Available tools:
- products.search: Find products by keyword
- products.detail: Get full product information
- categories.list: Browse product categories
- cart.preview: View shopping cart
- orders.status: Check order status

When users ask about products, use the appropriate tool.
```

---

## Contributing

This package is under active development. Contributions welcome!

1. Fork the repository
2. Create a feature branch
3. Submit a pull request

---

## License

MIT License - See [LICENSE](LICENSE) file for details.

---

## Support

- **Issues**: [GitHub Issues](https://github.com/heygeeks/bagisto-mcp/issues)
- **Email**: mohit@heygeeks.in
