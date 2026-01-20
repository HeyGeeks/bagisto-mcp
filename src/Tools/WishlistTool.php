<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Customer\Repositories\WishlistRepository;
use Illuminate\Support\Facades\Auth;
use HeyGeeks\BagistoMCP\Tools\Traits\AuthenticatedToolTrait;

class WishlistTool
{
    use AuthenticatedToolTrait;

    protected $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    /**
     * View the customer wishlist items.
     * 
     * Shows all saved products the customer wants to buy later.
     * Requires authentication.
     * Use this tool when the user asks about their wishlist, saved items, or favorites.
     * 
     * @param string $token Authentication token from customer.login
     * @return array Wishlist items
     */
    #[McpTool(name: 'wishlist_view')]
    public function view(string $token): array
    {
        if (!$this->authenticate($token)) {
            return [
                'authenticated' => false,
                'error' => 'Unauthenticated or Invalid Token. Please provide a valid token obtained from customer.login.',
            ];
        }

        $user = Auth::guard('sanctum')->user();

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
