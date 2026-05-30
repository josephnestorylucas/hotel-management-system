<?php
namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Floor extends Model {
    use HasUuid, HasSoftDelete;

    protected $fillable = ['building_id', 'name', 'floor_number', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'deleted_at' => 'datetime'];

    public function building(): BelongsTo {
        return $this->belongsTo(Building::class);
    }

    public function rooms(): HasMany {
        return $this->hasMany(Room::class);
    }
}