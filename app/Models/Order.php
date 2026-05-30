<?php

namespace App\Models;

use App\Contracts\ReceiptPrintable;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Order extends Model implements ReceiptPrintable
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'order_number', 'location_id', 'table_id', 'order_type',
        'booking_id', 'customer_name', 'customer_phone', 'status',
        'subtotal', 'discount', 'tax', 'total',
        'payment_method', 'payment_reference', 'notes', 'created_by', 'settled_by', 'settled_at',
        'order_source', 'bartender_status', 'bartender_status_updated_at',
        'stock_deducted_at', 'stock_reversed_at', 'billed_to_folio_at', 'billing_error',
        'guest_completed_at',
    ];

    protected $casts = [
        'subtotal'   => 'decimal:2',
        'discount'   => 'decimal:2',
        'tax'        => 'decimal:2',
        'total'      => 'decimal:2',
        'settled_at' => 'datetime',
        'bartender_status_updated_at' => 'datetime',
        'stock_deducted_at'           => 'datetime',
        'stock_reversed_at'           => 'datetime',
        'billed_to_folio_at'          => 'datetime',
        'guest_completed_at'          => 'datetime',
        'deleted_at'                  => 'datetime',
    ];

    /**
     * Auto-generate order number before creating.
     */
    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $location = StockLocation::find($order->location_id);
            $prefix   = strtoupper(substr($location->code ?? 'ORD', 0, 3));
            $count    = self::whereDate('created_at', today())->count() + 1;
            $order->order_number = $prefix . '-' . date('Ymd') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        });
    }

    /**
     * Recalculate totals from order items.
     */
    public function recalculate(): void
    {
        $this->load('items');
        $subtotal = $this->items->where('status', '!=', 'cancelled')->sum('subtotal');
        $this->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal - $this->discount + $this->tax,
        ]);
    }

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function table()     { return $this->belongsTo(Table::class); }
    public function items()     { return $this->hasMany(OrderItem::class); }
    public function creator()   { return $this->belongsTo(User::class, 'created_by'); }
    public function settler()   { return $this->belongsTo(User::class, 'settled_by'); }
    public function charge()    { return $this->hasOne(BookingCharge::class); }
    public function booking()   { return $this->belongsTo(Booking::class); }

    // ── Receipt Relationship ─────────────────────────────────────────────────

    public function receipt(): MorphOne
    {
        return $this->morphOne(Receipt::class, 'receiptable');
    }

    // ── ReceiptPrintable Implementation ──────────────────────────────────────

    public function toReceiptData(): array
    {
        $this->loadMissing(['items.menuItem', 'settler', 'location', 'table', 'booking']);

        $items = $this->items->where('status', '!=', 'cancelled')->map(function ($item) {
            $optionDetails = collect($item->selected_options_snapshot ?? [])
                ->map(function ($group) {
                    $values = collect($group['values'] ?? [])->pluck('label')->filter()->implode(', ');
                    if ($values === '') {
                        return null;
                    }

                    return ($group['group_name'] ?? 'Option') . ': ' . $values;
                })
                ->filter()
                ->implode(' | ');

            return [
                'name'       => $item->item_name_snapshot ?? $item->menuItem?->name ?? 'Item',
                'details'    => $optionDetails !== '' ? $optionDetails : null,
                'quantity'   => $item->quantity,
                'unit_price' => $item->unit_price,
                'amount'     => $item->subtotal,
            ];
        })->toArray();

        $module = $this->location?->slug === 'bar' ? 'bar' : 'restaurant';
        $customerName = $this->customer_name ?? $this->booking?->guest_name ?? null;

        return [
            'receipt_no'            => $this->order_number,
            'issued_at'             => $this->settled_at ?? $this->created_at,
            'module'                => $module,
            'customer_name'         => $customerName,
            'customer_phone'        => $this->customer_phone ?? $this->booking?->guest?->phone ?? null,
            'items'                 => $items,
            'subtotal'              => (float) $this->subtotal,
            'discount'              => (float) $this->discount,
            'tax'                   => (float) $this->tax,
            'total'                 => (float) $this->total,
            'amount_paid'           => $this->isPaid() ? (float) $this->total : 0.0,
            'balance'               => $this->isPaid() ? 0.0 : (float) $this->total,
            'currency'              => 'TZS',
            'payment_method'        => $this->payment_method,
            'payment_status'        => $this->getPaymentStatus(),
            'transaction_reference' => $this->payment_reference,
            'cashier'               => $this->settler?->name,
            'notes'                 => $this->notes,
        ];
    }

    public function getReceiptModule(): string
    {
        return $this->location?->slug === 'bar' ? 'bar' : 'restaurant';
    }

    public function isPaid(): bool
    {
        return $this->status === 'settled' && $this->settled_at !== null;
    }

    protected function getPaymentStatus(): string
    {
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }

        return $this->isPaid() ? 'paid' : 'unpaid';
    }
}
