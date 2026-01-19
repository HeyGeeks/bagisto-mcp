<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Product\Repositories\ProductRepository;

class ProductListTool extends BaseTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function name(): string
    {
        return 'products.list';
    }

    public function description(): string
    {
        return 'List products from the Bagisto store with optional filtering by category, price range, and pagination. Use this tool when the user wants to browse products, view catalog, or filter products by specific criteria.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of products to return (default: 10)',
                    'default' => 10,
                ],
                'page' => [
                    'type' => 'integer',
                    'description' => 'Page number for pagination (default: 1)',
                    'default' => 1,
                ],
                'category_id' => [
                    'type' => 'integer',
                    'description' => 'Filter products by category ID',
                ],
                'price_min' => [
                    'type' => 'number',
                    'description' => 'Minimum price filter',
                ],
                'price_max' => [
                    'type' => 'number',
                    'description' => 'Maximum price filter',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $arguments): array
    {
        $params = [];

        if (isset($arguments['category_id'])) {
            $params['category_id'] = $arguments['category_id'];
        }

        if (isset($arguments['price_min'])) {
            $params['price'] = $arguments['price_min'] . ',' . ($arguments['price_max'] ?? 1000000);
        }

        // Set limit for pagination
        $limit = $arguments['limit'] ?? 10;
        $page = $arguments['page'] ?? 1;

        // Merge into request for the repository to pick up
        request()->merge([
            'limit' => $limit,
            'page' => $page,
        ]);

        // getAll() returns a LengthAwarePaginator directly
        $products = $this->productRepository->getAll($params);

        $result = [];
        foreach ($products as $product) {
            $result[] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
                'currency' => core()->getCurrentCurrencyCode(),
                'in_stock' => $product->haveSufficientQuantity(1),
                'url' => $product->url_key ? route('shop.product_or_category.index', $product->url_key) : null,
            ];
        }

        return [
            'products' => $result,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'total_pages' => $products->lastPage(),
                'total_items' => $products->total(),
                'per_page' => $products->perPage(),
            ],
        ];
    }
}
