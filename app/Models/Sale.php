<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity', 'total'];

    // Relationship with Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper to get subtotal (quantity * product price)
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->product->price;
    }
}
