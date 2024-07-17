<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    protected $hidden = ['room_id', 'customer_id'];

    protected $fillable = [
        'room_id',
        'customer_id',
        'start_time',
        'end_time',
        'status'
    ];

    public function room(): BelongsTo {
        return $this->belongsTo(Room::class);
    }

    public function customer(): BelongsTo {
        return $this->belongsTo(Customer::class);
    }
}