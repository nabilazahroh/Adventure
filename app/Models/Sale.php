<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    // Daftar kolom yang dapat diisi secara massal
    protected $fillable = [
        'transaction_date',
        'checkout_code',
        'product_id',
        'quantity',
        'selling_price',
        'total',
        'profit',
        'notes',
        'discount_percent',
        'user_id',
    ];

    // Konversi tipe data
    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'integer',
        'selling_price' => 'decimal:2',
        'total' => 'decimal:2',
        'profit' => 'decimal:2',
        'discount_percent' => 'integer',
    ];

    // Relasi ke model Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke model User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
