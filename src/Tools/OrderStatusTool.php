<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderStatusTool extends BaseTool
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function name(): string
    {
        return 'orders.status';
    }

    public function description(): string
    {
        return 'Check the status of an order by order ID. Returns order status, payment state, shipment tracking, and estimated delivery. Use this tool when the user asks about their order status, tracking, or delivery information.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'order_id' => [
                    'type' => 'string',
                    'description' => 'The order increment ID (e.g., "100001")',
                ],
            ],
            'required' => ['order_id'],
        ];
    }

    public function execute(array $arguments): array
    {
        $orderId = $arguments['order_id'] ?? null;

        if (!$orderId) {
            return ['error' => 'Order ID is required'];
        }

        $order = $this->orderRepository->findOneByField('increment_id', $orderId);

        if (!$order) {
            return [
                'found' => false,
                'error' => 'Order not found',
                'order_id' => $orderId,
            ];
        }

        // Security: If user is logged in, ensure they own the order
        $user = Auth::guard('sanctum')->user();
        if ($user && $order->customer_id !== $user->id) {
            return [
                'found' => false,
                'error' => 'Unauthorized access to this order',
            ];
        }

        // Gather shipment info
        $tracking = null;
        $carrier = null;
        $estimatedDelivery = null;

        foreach ($order->shipments as $shipment) {
            $tracking = $shipment->track_number;
            $carrier = $shipment->carrier_title;
        }

        return [
            'found' => true,
            'order' => [
                'order_id' => $order->increment_id,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'payment_status' => $order->payment->method ?? 'unknown',
                'grand_total' => $order->grand_total,
                'currency' => $order->order_currency_code,
                'items_count' => $order->total_item_count,
                'created_at' => $order->created_at->toIso8601String(),
            ],
            'shipping' => [
                'tracking_number' => $tracking,
                'carrier' => $carrier,
                'estimated_delivery' => $estimatedDelivery,
            ],
        ];
    }
}
