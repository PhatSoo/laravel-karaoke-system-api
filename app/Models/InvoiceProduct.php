<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProduct extends Model
{
    use HasFactory;

    protected $table = 'invoices_products';

    protected $hidden = [
        'id',
        'invoice_id',
        'product_id'
    ];

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity'
    ];

    protected $with = ['product'];

    public function invoice(): BelongsTo {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo {
        return $this->belongsTo(Product::class);
    }
}