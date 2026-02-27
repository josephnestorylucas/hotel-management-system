<?php
// app/Http/Controllers/Procurement/DashboardController.php

namespace App\Http\Controllers\Procurement;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceivedNote;
use App\Models\LocalPurchaseOrder;
use App\Models\Supplier;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $summary = [
            'active_suppliers' => Supplier::where('is_active', true)->count(),
            'pending_lpos' => LocalPurchaseOrder::where('status', 'pending_approval')->count(),
            'active_lpos' => LocalPurchaseOrder::whereIn('status', ['approved', 'sent', 'partially_received'])->count(),
            'pending_grns' => GoodsReceivedNote::where('status', 'pending_confirmation')->count(),
        ];

        $recentLpos = LocalPurchaseOrder::with(['supplier', 'creator'])
            ->whereIn('status', ['pending_approval', 'approved', 'sent'])
            ->latest()
            ->take(10)
            ->get();

        $recentGrns = GoodsReceivedNote::with(['lpo', 'receiver'])
            ->whereIn('status', ['draft', 'pending_confirmation'])
            ->latest()
            ->take(10)
            ->get();

        return view('procurement.dashboard.index', compact('summary', 'recentLpos', 'recentGrns'));
    }
}