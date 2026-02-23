<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasUuid;

    protected $fillable = [
        'title', 'body', 'sms_message', 'type', 'target',
        'channels', 'scheduled_at', 'sent_at', 'recipients_count',
        'status', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at'      => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for drafts.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for sent broadcasts.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
