<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Invoice extends Model
{
    use HasFactory;

    protected $hidden = ['booking_id', 'staff_id'];

    protected $with = ['staff', 'booking'];

    protected $fillable = [
        'booking_id',
        'staff_id',
        'total_amount',
        'payment_method',
        'payment_status',
    ];

    public function booking(): BelongsTo {
        return $this->belongsTo(Booking::class);
    }

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function products(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'invoices_products')->withPivot('quantity')->withTimestamps();
    }
}