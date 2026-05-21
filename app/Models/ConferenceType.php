<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceType extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
