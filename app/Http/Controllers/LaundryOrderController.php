<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\LaundryItem;
use App\Models\Booking;
use App\Models\BookingCharge;
use Illuminate\Http\Request;

class LaundryOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = LaundryOrder::with(['booking.room', 'guest', 'creator', 'items.laundryItem'])
            ->orderBy('created_at', 'desc');

        // House help can only see orders (read-only overview)
        // Front desk, supervisor, admin see all

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('guest', function ($gq) use ($search) {
                      $gq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('booking', function ($bq) use ($search) {
                      $bq->where('booking_number', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->paginate(20);

        $stats = [
            'pending' => LaundryOrder::where('status', 'pending')->count(),
            'in_progress' => LaundryOrder::where('status', 'in_progress')->count(),
            'completed' => LaundryOrder::where('status', 'completed')->count(),
            'delivered' => LaundryOrder::where('status', 'delivered')->count(),
        ];

        return view('laundry-orders.index', compact('orders', 'stats'));
    }

    public function create()
    {
        // Only checked-in bookings can have laundry
        $bookings = Booking::with(['room', 'guest'])
            ->where('status', 'checked_in')
            ->orderBy('created_at', 'desc')
            ->get();

        $laundryItems = LaundryItem::where('is_active', true)->orderBy('name')->get();

        return view('laundry-orders.create', compact('bookings', 'laundryItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|uuid|exists:bookings,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.laundry_item_id' => 'required|uuid|exists:laundry_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $booking = Booking::with('guest')->findOrFail($validated['booking_id']);

        // Ensure booking is checked in
        if ($booking->status !== 'checked_in') {
            return back()->withInput()
                ->with('error', 'Laundry can only be requested for checked-in guests.');
        }

        // Create the order
        $order = LaundryOrder::create([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'status' => 'pending',
            'notes' => $validated['notes'],
            'created_by' => auth()->id(),
        ]);

        // Create order items
        $totalAmount = 0;
        foreach ($validated['items'] as $itemData) {
            $laundryItem = LaundryItem::findOrFail($itemData['laundry_item_id']);
            $quantity = $itemData['quantity'];
            $unitPrice = $laundryItem->price;
            $totalPrice = $quantity * $unitPrice;
            $totalAmount += $totalPrice;

            LaundryOrderItem::create([
                'laundry_order_id' => $order->id,
                'laundry_item_id' => $laundryItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        }

        $order->update(['total_amount' => $totalAmount]);

        return redirect()->route('laundry-orders.index')
            ->with('success', 'Laundry order #' . $order->order_number . ' created successfully. Total: ' . number_format($totalAmount));
    }

    public function show(LaundryOrder $laundryOrder)
    {
        $laundryOrder->load(['booking.room', 'guest', 'creator', 'items.laundryItem', 'bookingCharge']);
        return view('laundry-orders.show', compact('laundryOrder'));
    }

    public function edit(LaundryOrder $laundryOrder)
    {
        if (!in_array($laundryOrder->status, ['pending'])) {
            return back()->with('error', 'Only pending orders can be edited.');
        }

        $bookings = Booking::with(['room', 'guest'])
            ->where('status', 'checked_in')
            ->orderBy('created_at', 'desc')
            ->get();

        $laundryItems = LaundryItem::where('is_active', true)->orderBy('name')->get();
        $laundryOrder->load('items.laundryItem');

        return view('laundry-orders.edit', compact('laundryOrder', 'bookings', 'laundryItems'));
    }

    public function update(Request $request, LaundryOrder $laundryOrder)
    {
        if (!in_array($laundryOrder->status, ['pending'])) {
            return back()->with('error', 'Only pending orders can be updated.');
        }

        $validated = $request->validate([
            'booking_id' => 'required|uuid|exists:bookings,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.laundry_item_id' => 'required|uuid|exists:laundry_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $booking = Booking::with('guest')->findOrFail($validated['booking_id']);

        if ($booking->status !== 'checked_in') {
            return back()->withInput()
                ->with('error', 'Laundry can only be requested for checked-in guests.');
        }

        $laundryOrder->update([
            'booking_id' => $booking->id,
            'guest_id' => $booking->guest_id,
            'notes' => $validated['notes'],
        ]);

        // Replace items
        $laundryOrder->items->each(fn($item) => $this->softDelete($item));

        $totalAmount = 0;
        foreach ($validated['items'] as $itemData) {
            $laundryItem = LaundryItem::findOrFail($itemData['laundry_item_id']);
            $quantity = $itemData['quantity'];
            $unitPrice = $laundryItem->price;
            $totalPrice = $quantity * $unitPrice;
            $totalAmount += $totalPrice;

            LaundryOrderItem::create([
                'laundry_order_id' => $laundryOrder->id,
                'laundry_item_id' => $laundryItem->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
            ]);
        }

        $laundryOrder->update(['total_amount' => $totalAmount]);

        return redirect()->route('laundry-orders.index')
            ->with('success', 'Laundry order updated successfully.');
    }

    public function destroy(LaundryOrder $laundryOrder)
    {
        if (!in_array($laundryOrder->status, ['pending'])) {
            return back()->with('error', 'Only pending orders can be deleted.');
        }

        $laundryOrder->items->each(fn($item) => $this->softDelete($item));
        $this->softDelete($laundryOrder);

        return redirect()->route('laundry-orders.index')
            ->with('success', 'Laundry order deleted successfully.');
    }

    // Status transitions
    public function markInProgress(LaundryOrder $laundryOrder)
    {
        if (!$laundryOrder->markAsInProgress()) {
            return back()->with('error', 'Order cannot be marked as in progress.');
        }
        return back()->with('success', 'Order marked as in progress.');
    }

    public function markCompleted(LaundryOrder $laundryOrder)
    {
        if (!$laundryOrder->markAsCompleted()) {
            return back()->with('error', 'Order cannot be marked as completed.');
        }
        return back()->with('success', 'Order completed. Charge added to booking.');
    }

    public function markDelivered(LaundryOrder $laundryOrder)
    {
        if (!$laundryOrder->markAsDelivered()) {
            return back()->with('error', 'Order cannot be marked as delivered.');
        }
        return back()->with('success', 'Laundry delivered to guest.');
    }

    // API: get laundry items as JSON (for dynamic form)
    public function getItems()
    {
        $items = LaundryItem::where('is_active', true)->orderBy('name')->get(['id', 'name', 'price']);
        return response()->json($items);
    }
}
