<?php

namespace App\Services;

use App\Models\BarTicket;
use App\Models\KitchenTicket;
use App\Models\Order;
use App\Models\RoomCharge;
use Illuminate\Support\Facades\Log;

class OrderDispatchService
{
    /**
     * Split order items by destination and create KOT/BOT tickets.
     */
    public function splitAndDispatch(Order $order): void
    {
        $order->load('items.menuItem');

        $kitchenItems = $order->items
            ->where('status', '!=', 'cancelled')
            ->filter(fn($item) => ($item->menuItem->destination ?? 'kitchen') === 'kitchen');

        $barItems = $order->items
            ->where('status', '!=', 'cancelled')
            ->filter(fn($item) => ($item->menuItem->destination ?? 'kitchen') === 'bar');

        if ($kitchenItems->isNotEmpty()) {
            KitchenTicket::create([
                'order_id'   => $order->id,
                'table_id'   => $order->table_id,
                'items'      => $kitchenItems->map(fn($item) => [
                    'id'       => $item->id,
                    'name'     => $item->item_name_snapshot ?? $item->menuItem?->name ?? 'Item',
                    'quantity' => $item->quantity,
                    'notes'    => $item->notes,
                    'options'  => $item->selected_options_snapshot,
                ])->toArray(),
                'status'     => 'pending',
                'printed_at' => now(),
            ]);
        }

        if ($barItems->isNotEmpty()) {
            BarTicket::create([
                'order_id'   => $order->id,
                'table_id'   => $order->table_id,
                'items'      => $barItems->map(fn($item) => [
                    'id'       => $item->id,
                    'name'     => $item->item_name_snapshot ?? $item->menuItem?->name ?? 'Item',
                    'quantity' => $item->quantity,
                    'notes'    => $item->notes,
                    'options'  => $item->selected_options_snapshot,
                ])->toArray(),
                'status'     => 'pending',
                'printed_at' => now(),
            ]);
        }
    }

    /**
     * Post room service charges to guest folio.
     */
    public function postRoomCharge(Order $order, string $bookingId): void
    {
        RoomCharge::create([
            'booking_id' => $bookingId,
            'order_id'   => $order->id,
            'description' => 'Room Service - Order #' . $order->order_number,
            'amount'      => $order->total,
            'charged_at'  => now(),
        ]);
    }
}
