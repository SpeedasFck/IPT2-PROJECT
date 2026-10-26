<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'confirmation_token', 'user_id', 'customer_name', 'email', 'phone', 'delivery_address',
        'city', 'province', 'postal_code', 'subtotal', 'delivery_fee', 'total',
        'payment_method', 'payment_status', 'status',
    ];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'delivery_fee' => 'decimal:2', 'total' => 'decimal:2'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}