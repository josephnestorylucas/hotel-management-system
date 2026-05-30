<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTransaction extends Model
{
    use HasUuid, HasSoftDelete;

    public $timestamps = false;

    protected $fillable = [
        'guest_id', 'type', 'points', 'balance_after',
        'source', 'reference_id', 'notes', 'created_by', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
