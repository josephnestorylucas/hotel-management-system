<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Building extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'code', 'address', 'is_active'];
    
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function floors(): HasMany
    {
        return $this->hasMany(Floor::class);
    }

    // Add this relationship
    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(
            Room::class,
            Floor::class,
            'building_id', // Foreign key on Floor table
            'floor_id',    // Foreign key on Room table
            'id',          // Local key on Building table
            'id'           // Local key on Floor table
        );
    }
}