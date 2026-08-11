<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'description', 'price', 'quantity', 'low_stock_threshold'];

    // app/Models/Product.php — add to boot
protected static function booted()
{
    static::updated(function (Product $product) {
        if ($product->wasChanged('quantity') && $product->isLowStock()) {
            $approvalAdmins = \App\Models\User::role('approval_admin')->get();
            \Illuminate\Support\Facades\Notification::send($approvalAdmins, new \App\Notifications\LowStockAlert($product));
        }
    });
}

    public function stockRequests()
    {
        return $this->hasMany(StockUpdateRequest::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }
}
