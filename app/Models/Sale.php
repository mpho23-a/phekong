<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = ['product_id', 'sales_rep_id', 'quantity_sold', 'sale_date', 'notes'];

    protected $casts = ['sale_date' => 'date'];

    public function product() { return $this->belongsTo(Product::class); }
    public function salesRep() { return $this->belongsTo(User::class, 'sales_rep_id'); }
}