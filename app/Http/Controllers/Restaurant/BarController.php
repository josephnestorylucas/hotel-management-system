<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\BarTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarController extends Controller
{
    public function queue(Request $request): View
    {
        $tickets = BarTicket::with(['order', 'table'])
            ->whereIn('status', ['pending', 'preparing'])
            ->latest()
            ->get();

        return view('restaurant.bar.queue', compact('tickets'));
    }

    public function tabs(Request $request): View
    {
        $tabs = \App\Models\Order::with(['items.menuItem', 'table'])
            ->where('order_type', 'bar_tab')
            ->whereIn('status', ['open', 'sent', 'ready'])
            ->latest()
            ->get();

        return view('restaurant.bar.tabs', compact('tabs'));
    }

    public function markPreparing(BarTicket $ticket): RedirectResponse
    {
        $ticket->markPreparing();

        return redirect()->route('restaurant.bar.queue')
            ->with('success', 'Ticket marked as preparing.');
    }

    public function markReady(BarTicket $ticket): RedirectResponse
    {
        $ticket->markReady();

        return redirect()->route('restaurant.bar.queue')
            ->with('success', 'Ticket marked as ready.');
    }
}
