<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\Auth;

class CartPreviewTool extends BaseTool
{
    public function name(): string
    {
        return 'cart.preview';
    }

    public function description(): string
    {
        return 'Preview the current shopping cart contents (read-only). Shows items, quantities, prices, and totals. Use this tool when the user asks about their cart, what items they have added, or wants to review before checkout.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
            'required' => [],
            'description' => 'No input required. Cart is identified by session or authenticated user.',
        ];
    }

    public function execute(array $arguments): array
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
