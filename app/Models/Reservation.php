<?php
namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model {
    use HasUuid;

    protected $fillable = [
        'reservation_number', 'room_id', 'guest_name', 'guest_phone', 'guest_email',
        'check_in_date', 'check_out_date', 'number_of_guests', 'status', 'total_amount', 'created_by'
    ];

    protected $casts = [
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'total_amount' => 'decimal:2'
    ];

    protected static function boot() {
        parent::boot();
        static::creating(function ($reservation) {
            $reservation->reservation_number = 'RES-' . strtoupper(uniqid());
        });
    }

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function creator(): BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }
}