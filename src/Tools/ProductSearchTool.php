<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Product\Repositories\ProductRepository;

class ProductSearchTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Search for products in the Bagisto store using full-text search.
     * 
     * Use this tool when the user is looking for a specific product by name, keyword, or description.
     * Returns relevant products sorted by relevance.
     * 
     * @param string $query Search query string (product name, keyword, or description)
     * @param int $limit Maximum number of results to return (default: 20)
     * @return array Search results
     */
    #[McpTool(name: 'products_search')]
    public function search(string $query, int $limit = 20): array
    {
        if (empty($query)) {
            return ['products' => [], 'message' => 'Query is required'];
        }

        // Merge into request for the repository to pick up
        request()->merge([
            'query' => $query,
            'name' => $query,
            'limit' => $limit,
        ]);

        // getAll() returns a LengthAwarePaginator directly
        $products = $this->productRepository->getAll(['name' => $query]);

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'description' => strip_tags($product->short_description ?? ''),
                'price' => $product->price,
                'currency' => core()->getCurrentCurrencyCode(),
                'url' => $product->url_key ? route('shop.product_or_category.index', $product->url_key) : null,
            ];
        }

        return [
            'query' => $query,
            'results_count' => count($result),
            'products' => $result,
        ];
    }
}
