<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class KitchenStockItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'name', 'unit', 'current_quantity', 'minimum_quantity', 'is_active',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function movements()
    {
        return $this->hasMany(KitchenStockMovement::class)->latest();
    }

    public function isLow(): bool
    {
        return $this->current_quantity <= $this->minimum_quantity;
    }
}
