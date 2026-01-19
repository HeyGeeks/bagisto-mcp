<?php

namespace HeyGeeks\BagistoMCP\Tests\Feature;

use Tests\TestCase;
use Mockery;

class MCPTest extends TestCase
{
    public function test_mcp_endpoint_exists()
    {
        $response = $this->postJson('/mcp', [
            'tool' => 'invalid.tool'
        ]);

        $this->assertEquals(404, $response->status());
    }

    public function test_products_list_tool()
    {
        // Use an anonymous class to mock the Product model behavior
        $product = new class {
            public $id = 1;
            public $sku = 'TEST-1';
            public $name = 'Test Product';
            public $price = 100;
            public $url_key = 'test-product';
            public function haveSufficientQuantity($qty)
            {
                return true;
            }
        };

        $this->mock(\Webkul\Product\Repositories\ProductRepository::class, function ($mock) use ($product) {
            $mock->shouldReceive('getAll')->andReturnSelf();
            $mock->shouldReceive('paginate')->andReturn(new \Illuminate\Pagination\LengthAwarePaginator(
                collect([$product]),
                1,
                5
            ));
        });

        $response = $this->postJson('/mcp', [
            'tool' => 'products.list',
            'arguments' => ['limit' => 5]
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['sku' => 'TEST-1']);
    }

    public function test_products_search_tool()
    {
        // Simple object for search since it only reads properties
        $product = (object) [
            'id' => 2,
            'sku' => 'SEARCH-1',
            'name' => 'Search Result',
            'short_description' => 'Desc',
            'price' => 50,
            'url_key' => 'search-result'
        ];

        $this->mock(\Webkul\Product\Repositories\ProductRepository::class, function ($mock) use ($product) {
            $mock->shouldReceive('getAll')->andReturnSelf();
            $mock->shouldReceive('paginate')->andReturn(new \Illuminate\Pagination\LengthAwarePaginator(
                collect([$product]),
                1,
                20
            ));
        });

        $response = $this->postJson('/mcp', [
            'tool' => 'products.search',
            'arguments' => ['query' => 'test']
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['sku' => 'SEARCH-1']);
    }

    public function test_invalid_tool_returns_404()
    {
        $response = $this->postJson('/mcp', [
            'tool' => 'invalid.tool'
        ]);

        $response->assertStatus(404);
    }
}
