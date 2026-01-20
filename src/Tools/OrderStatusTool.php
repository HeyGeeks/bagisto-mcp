<?php

namespace HeyGeeks\BagistoMCP\Tools;

use Mcp\Capability\Attribute\McpTool;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\Auth;

class OrderStatusTool
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        file_put_contents('debug_tool.log', "OrderStatusTool __construct called\n", FILE_APPEND);
        $this->orderRepository = $orderRepository;
    }

    /**
     * Check the status of an order by order ID.
     * 
     * Returns order status, payment state, shipment tracking, and estimated delivery.
     * Use this tool when the user asks about their order status, tracking, or delivery information.
     * 
     * @param string $order_id The order increment ID (e.g., "100001")
     * @return array The order status details
     */
    #[McpTool(name: 'orders_status')]
    public function checkStatus(string $order_id): array
    {
        file_put_contents('debug_tool.log', "checkStatus called with ID: $order_id\n", FILE_APPEND);
        if (!$order_id) {
            return ['error' => 'Order ID is required'];
        }

        $order = $this->orderRepository->findOneByField('increment_id', $order_id);

        if (!$order) {
            return [
                'found' => false,
                'error' => 'Order not found',
                'order_id' => $order_id,
            ];
        }

        // Security: If user is logged in, ensure they own the order
        // Note: Auth::guard('sanctum')->user() might contextually depend on how the request is handled (CLI vs HTTP)
        // In StdioTransport, there might be no auth context unless explicitly passed or mocked.
        // We will keep it for now but it might return null.
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
