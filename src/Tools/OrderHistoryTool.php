<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderHistoryTool extends BaseTool
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function name(): string
    {
        return 'orders.history';
    }

    public function description(): string
    {
        return 'Get the order history for the authenticated customer. Lists all past orders with status, totals, and dates. Requires authentication. Use this tool when the user asks about their past orders, purchase history, or wants to find a specific order.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of orders to return (default: 10)',
                    'default' => 10,
                ],
                'status' => [
                    'type' => 'string',
                    'description' => 'Filter by order status (e.g., "pending", "completed", "canceled")',
                    'enum' => ['pending', 'processing', 'completed', 'canceled', 'closed'],
                ],
            ],
            'required' => [],
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

        $limit = $arguments['limit'] ?? 10;
        $status = $arguments['status'] ?? null;

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
