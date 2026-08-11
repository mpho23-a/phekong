<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Product;
use App\Models\StockUpdateRequest;
use App\Notifications\StockRequestSubmitted;
use App\Notifications\LowStockAlert;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;

class StockUpdateRequestController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'requested_quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:500',
        ]);

        $stockRequest = StockUpdateRequest::create([
            'product_id' => $product->id,
            'current_quantity' => $product->quantity,
            'requested_quantity' => $validated['requested_quantity'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        $approvalAdmins = User::role('approval_admin')->get();
        Notification::send($approvalAdmins, new StockRequestSubmitted($stockRequest));

        return back()->with('success', 'Stock update request submitted for approval.');
    }

    public function index()
    {
        $pending = StockUpdateRequest::with(['product', 'requester'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('stock-requests.index', compact('pending'));
    }

    public function approve(StockUpdateRequest $stockUpdateRequest)
    {
        $stockUpdateRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        $product = $stockUpdateRequest->product;
        $product->update(['quantity' => $stockUpdateRequest->requested_quantity]);

        if ($product->isLowStock()) {
            $approvalAdmins = User::role('approval_admin')->get();
            Notification::send($approvalAdmins, new LowStockAlert($product));
        }

        return back()->with('success', 'Stock update approved.');
    }

    public function reject(StockUpdateRequest $stockUpdateRequest)
    {
        $stockUpdateRequest->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'decided_at' => now(),
        ]);

        return back()->with('success', 'Stock update rejected.');
    }
}
