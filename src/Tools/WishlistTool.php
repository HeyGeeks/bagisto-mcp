<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Customer\Repositories\WishlistRepository;
use Illuminate\Support\Facades\Auth;

class WishlistTool extends BaseTool
{
    protected $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    public function name(): string
    {
        return 'wishlist.view';
    }

    public function description(): string
    {
        return 'View the customer wishlist items. Shows all saved products the customer wants to buy later. Requires authentication. Use this tool when the user asks about their wishlist, saved items, or favorites.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
            'description' => 'No input required. Requires authentication via Bearer token.',
        ];
    }

    public function execute(array $arguments): array
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return [
                'authenticated' => false,
                'error' => 'Authentication required. Please login using customer.login first.',
            ];
        }

        $wishlistItems = $this->wishlistRepository->findWhere(['customer_id' => $user->id]);

        $items = [];
        foreach ($wishlistItems as $item) {
            $product = $item->product;
            if ($product) {
                $items[] = [
                    'wishlist_id' => $item->id,
                    'product_id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => $product->price,
                    'in_stock' => $product->haveSufficientQuantity(1),
                    'added_at' => $item->created_at->toIso8601String(),
                    'url' => route('shop.product_or_category.index', $product->url_key),
                ];
            }
        }

        return [
            'authenticated' => true,
            'items_count' => count($items),
            'items' => $items,
        ];
    }
}
