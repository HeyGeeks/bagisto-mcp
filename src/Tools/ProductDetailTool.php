<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Product\Repositories\ProductRepository;

class ProductDetailTool
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get detailed information about a specific product by ID or SKU.
     * 
     * Use this tool when the user wants to know more about a particular product, including full description, attributes, images, and availability.
     * 
     * @param int|null $id Product ID
     * @param string|null $sku Product SKU (alternative to ID)
     * @return array Product details
     */
    #[McpTool(name: 'products_detail')]
    public function detail(?int $id = null, ?string $sku = null): array
    {
        $product = null;

        if ($id) {
            $product = $this->productRepository->find($id);
        } elseif ($sku) {
            $product = $this->productRepository->findOneByField('sku', $sku);
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
