<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, Searchable, SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_id',
        'count',
        'price'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
