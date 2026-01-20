<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;

use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;
use HeyGeeks\BagistoMCP\Tools\Traits\AuthenticatedToolTrait;

class OrderHistoryTool
{
    use AuthenticatedToolTrait;

    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get the order history for the authenticated customer.
     * 
     * Lists all past orders with status, totals, and dates.
     * Requires authentication.
     * Use this tool when the user asks about their past orders, purchase history, or wants to find a specific order.
     * 
     * @param string $token Authentication token from customer.login
     * @param int $limit Maximum number of orders to return (default: 10)
     * @param string|null $status Filter by order status (e.g., "pending", "completed", "canceled")
     * @return array Order history
     */
    #[McpTool(name: 'orders_history')]
    public function history(string $token, int $limit = 10, ?string $status = null): array
    {
        if (!$this->authenticate($token)) {
            return [
                'authenticated' => false,
                'error' => 'Unauthenticated or Invalid Token. Please provide a valid token obtained from customer.login.',
            ];
        }

        $user = Auth::guard('sanctum')->user();

        $query = $this->orderRepository->query()
            ->where('customer_id', $user->id)
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->limit($limit)->get();

        $result = [];
        foreach ($orders as $order) {
            $result[] = [
                'order_id' => $order->increment_id,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'items_count' => $order->total_item_count,
                'grand_total' => $order->grand_total,
                'currency' => $order->order_currency_code,
                'created_at' => $order->created_at->toIso8601String(),
            ];
        }

        return [
            'authenticated' => true,
            'customer_id' => $user->id,
            'orders_count' => count($result),
            'orders' => $result,
        ];
    }
}
