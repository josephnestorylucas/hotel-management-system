<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LaundryServiceItem extends Model
{
    use HasUuid;

    protected $fillable = ['laundry_service_id', 'item_name', 'price', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(LaundryService::class, 'laundry_service_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
