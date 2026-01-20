<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class CartPreviewTool
{
    /**
     * Preview the current shopping cart contents (read-only).
     * 
     * Shows items, quantities, prices, and totals.
     * Use this tool when the user asks about their cart, what items they have added, or wants to review before checkout.
     * 
     * @return array The cart details
     */
    #[McpTool(name: 'cart_preview')]
    public function preview(): array
    {
        $cart = Cart::getCart();

        if (!$cart) {
            return [
                'has_items' => false,
                'message' => 'Cart is empty',
                'items' => [],
                'totals' => null,
            ];
        }

        $items = [];
        foreach ($cart->items as $item) {
            $items[] = [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'name' => $item->name,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'total' => $item->total,
                'currency' => $cart->cart_currency_code,
            ];
        }

        return [
            'has_items' => true,
            'items_count' => $cart->items_count,
            'items' => $items,
            'totals' => [
                'subtotal' => $cart->sub_total,
                'discount' => $cart->discount_amount ?? 0,
                'tax' => $cart->tax_total ?? 0,
                'grand_total' => $cart->grand_total,
                'currency' => $cart->cart_currency_code,
            ],
        ];
    }
}
