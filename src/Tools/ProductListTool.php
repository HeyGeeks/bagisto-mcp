<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Product\Repositories\ProductRepository;

class ProductListTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * List products from the Bagisto store.
     * 
     * Optional filtering by category, price range, and pagination.
     * Use this tool when the user wants to browse products, view catalog, or filter products by specific criteria.
     * 
     * @param int $limit Maximum number of products to return (default: 10)
     * @param int $page Page number for pagination (default: 1)
     * @param int|null $category_id Filter products by category ID
     * @param float|null $price_min Minimum price filter
     * @param float|null $price_max Maximum price filter
     * @return array Product list
     */
    #[McpTool(name: 'products_list')]
    public function list(
        int $limit = 10,
        int $page = 1,
        ?int $category_id = null,
        ?float $price_min = null,
        ?float $price_max = null
    ): array {
        $params = [];

        if ($category_id) {
            $params['category_id'] = $category_id;
        }

        if ($price_min !== null) {
            $params['price'] = $price_min . ',' . ($price_max ?? 1000000);
        }

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
