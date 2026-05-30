<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = ['name', 'location_id', 'description', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer', 'deleted_at' => 'datetime'];

    public function location()  { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function menuItems() { return $this->hasMany(MenuItem::class, 'category_id')->orderBy('name'); }
}
