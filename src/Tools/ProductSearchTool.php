<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Product\Repositories\ProductRepository;

class ProductSearchTool extends BaseTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function name(): string
    {
        return 'products.search';
    }

    public function description(): string
    {
        return 'Search for products in the Bagisto store using full-text search. Use this tool when the user is looking for a specific product by name, keyword, or description. Returns relevant products sorted by relevance.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => [
                    'type' => 'string',
                    'description' => 'Search query string (product name, keyword, or description)',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of results to return (default: 20)',
                    'default' => 20,
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function execute(array $arguments): array
    {
        $query = $arguments['query'] ?? '';

        if (empty($query)) {
            return ['products' => [], 'message' => 'Query is required'];
        }

        $limit = $arguments['limit'] ?? 20;

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
