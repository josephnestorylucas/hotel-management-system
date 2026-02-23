<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LaundryService extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'description', 'turnaround_hours', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function serviceItems()
    {
        return $this->hasMany(LaundryServiceItem::class);
    }

    public function activeItems()
    {
        return $this->hasMany(LaundryServiceItem::class)->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
