<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockUpdateRequest extends Model
{
    protected $fillable = [
        'product_id', 'current_quantity', 'requested_quantity',
        'reason', 'status', 'requested_by', 'approved_by', 'decided_at',
    ];

    protected $casts = ['decided_at' => 'datetime'];

    public function product() { return $this->belongsTo(Product::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
