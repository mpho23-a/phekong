<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['product_id', 'sales_rep_id', 'quantity_sold', 'price_at_sale', 'sale_date', 'notes'];

    protected $casts = [
        'sale_date' => 'date',
        'price_at_sale' => 'decimal:2',
    ];

    public function product() { return $this->belongsTo(Product::class); }
    public function salesRep() { return $this->belongsTo(User::class, 'sales_rep_id'); }

    public function getRevenueAttribute(): float
    {
        return $this->quantity_sold * $this->price_at_sale;
    }
}