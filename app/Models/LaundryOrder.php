<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaundryOrder extends Model
{
    use HasUuid;

    protected $fillable = [
        'order_number',
        'booking_id',
        'guest_id',
        'status',
        'total_amount',
        'notes',
        'created_by',
        'received_at',
        'completed_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'LO-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function bookingCharge(): HasOne
    {
        return $this->hasOne(BookingCharge::class, 'reference_id')
            ->where('charge_type', 'laundry');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    // Status transitions
    public function canStartProcessing(): bool
    {
        return $this->status === 'pending';
    }

    public function canMarkCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    public function canMarkDelivered(): bool
    {
        return $this->status === 'completed';
    }

    public function markAsInProgress(): bool
    {
        if (!$this->canStartProcessing()) {
            return false;
        }

        return $this->update([
            'status' => 'in_progress',
            'received_at' => now(),
        ]);
    }

    public function markAsCompleted(): bool
    {
        if (!$this->canMarkCompleted()) {
            return false;
        }

        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Auto-create booking charge when laundry is completed
        $this->createBookingCharge();

        return true;
    }

    public function markAsDelivered(): bool
    {
        if (!$this->canMarkDelivered()) {
            return false;
        }

        return $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    // Calculate total from items
    public function calculateTotal(): float
    {
        $total = $this->items()->sum('total_price');
        $this->update(['total_amount' => $total]);
        return $total;
    }

    // Create a booking charge when laundry is completed
    protected function createBookingCharge(): void
    {
        // Only create if one doesn't already exist
        if ($this->bookingCharge()->exists()) {
            return;
        }

        BookingCharge::create([
            'booking_id' => $this->booking_id,
            'charge_type' => 'laundry',
            'reference_id' => $this->id,
            'description' => 'Laundry Order #' . $this->order_number,
            'amount' => $this->total_amount,
            'status' => 'unpaid',
        ]);
    }

    // Accessors
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'purple',
            'delivered' => 'green',
            default => 'gray',
        };
    }
}
