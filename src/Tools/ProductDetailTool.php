<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Product\Repositories\ProductRepository;

class ProductDetailTool extends BaseTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function name(): string
    {
        return 'products.detail';
    }

    public function description(): string
    {
        return 'Get detailed information about a specific product by ID or SKU. Use this tool when the user wants to know more about a particular product, including full description, attributes, images, and availability.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => [
                    'type' => 'integer',
                    'description' => 'Product ID',
                ],
                'sku' => [
                    'type' => 'string',
                    'description' => 'Product SKU (alternative to ID)',
                ],
            ],
            'required' => [],
        ];
    }

    public function execute(array $arguments): array
    {
        $product = null;

        if (isset($arguments['id'])) {
            $product = $this->productRepository->find($arguments['id']);
        } elseif (isset($arguments['sku'])) {
            $product = $this->productRepository->findOneByField('sku', $arguments['sku']);
        } else {
            return [
                'found' => false,
                'error' => 'Either id or sku is required',
            ];
        }

        if (!$product) {
            return [
                'found' => false,
                'error' => 'Product not found',
            ];
        }

        $images = [];
        if ($product->images) {
            foreach ($product->images as $image) {
                $images[] = $image->url ?? null;
            }
        }

        return [
            'found' => true,
            'product' => [
                'id' => $product->id,
                'sku' => $product->sku,
                'type' => $product->type,
                'name' => $product->name,
                'description' => strip_tags($product->description ?? ''),
                'short_description' => strip_tags($product->short_description ?? ''),
                'price' => $product->price,
                'special_price' => $product->special_price,
                'currency' => core()->getCurrentCurrencyCode(),
                'in_stock' => $product->haveSufficientQuantity(1),
                'url' => $product->url_key ? route('shop.product_or_category.index', $product->url_key) : null,
                'images' => array_filter($images),
            ],
        ];
    }
}
