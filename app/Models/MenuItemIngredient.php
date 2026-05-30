<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class MenuItemIngredient extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = ['menu_item_id', 'product_id', 'quantity', 'unit'];

    protected $casts = ['quantity' => 'decimal:4', 'deleted_at' => 'datetime'];

    public function menuItem() { return $this->belongsTo(MenuItem::class); }
    public function product()  { return $this->belongsTo(Product::class); }
}
