<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Models\KitchenTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KitchenController extends Controller
{
    public function queue(Request $request): View
    {
        $tickets = KitchenTicket::with(['order', 'table'])
            ->whereIn('status', ['pending', 'preparing'])
            ->latest()
            ->get();

        return view('restaurant.kitchen.queue', compact('tickets'));
    }

    public function markPreparing(KitchenTicket $ticket): RedirectResponse
    {
        $ticket->markPreparing();

        return redirect()->route('restaurant.kitchen.queue')
            ->with('success', 'Ticket marked as preparing.');
    }

    public function markReady(KitchenTicket $ticket): RedirectResponse
    {
        $ticket->markReady();

        return redirect()->route('restaurant.kitchen.queue')
            ->with('success', 'Ticket marked as ready.');
    }
}
