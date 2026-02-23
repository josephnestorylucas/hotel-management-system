<?php

namespace App\Http\Controllers\Laundry;

use App\Http\Controllers\Controller;
use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaundryServiceController extends Controller
{
    // GET /laundry/services
    public function index(): View
    {
        $services = LaundryService::with(['serviceItems' => fn ($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->get();

        return view('laundry.services.index', compact('services'));
    }

    // POST /laundry/services/{service}/items
    public function addItem(Request $request, LaundryService $service): RedirectResponse
    {
        $request->validate([
            'item_name' => 'required|string|max:100',
            'price'     => 'required|numeric|min:1',
        ]);

        LaundryServiceItem::updateOrCreate(
            ['laundry_service_id' => $service->id, 'item_name' => $request->item_name],
            ['price' => $request->price, 'is_active' => true]
        );

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "{$request->item_name} added to {$service->name}.");
    }

    // PUT /laundry/services/{service}/items/{item}
    public function updateItem(Request $request, LaundryService $service, LaundryServiceItem $item): RedirectResponse
    {
        $request->validate(['price' => 'required|numeric|min:1']);

        $item->update(['price' => $request->price]);

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "Price updated for {$item->item_name}.");
    }

    // DELETE /laundry/services/{service}/items/{item}
    public function removeItem(LaundryService $service, LaundryServiceItem $item): RedirectResponse
    {
        $item->update(['is_active' => false]);

        return redirect()
            ->route('laundry.services.index')
            ->with('success', "{$item->item_name} removed from {$service->name}.");
    }
}
