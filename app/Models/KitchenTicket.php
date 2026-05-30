<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class KitchenTicket extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'order_id', 'table_id', 'items', 'status', 'notes', 'printed_at',
    ];

    protected $casts = [
        'items'      => 'array',
        'printed_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function order() { return $this->belongsTo(Order::class); }
    public function table() { return $this->belongsTo(Table::class); }

    public function markPreparing(): void
    {
        $this->update(['status' => 'preparing']);
    }

    public function markReady(): void
    {
        $this->update(['status' => 'ready']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'preparing']);
    }
}
