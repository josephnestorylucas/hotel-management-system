<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StoreNotification extends Model
{
    use HasUuid, HasSoftDelete;

    public $timestamps = false;

    protected $table = 'store_notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'body',
        'is_read', 'reference_type', 'reference_id', 'action_url', 'created_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
