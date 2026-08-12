<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_date' => 'required|date',
            'sales' => 'required|array|min:1',
            'sales.*.product_id' => 'required|exists:products,id',
            'sales.*.quantity_sold' => 'nullable|integer|min:0',
        ]);

        // Only process rows where a quantity was actually entered
        $rows = collect($validated['sales'])->filter(fn ($row) => !empty($row['quantity_sold']) && $row['quantity_sold'] > 0);

        if ($rows->isEmpty()) {
            return back()->with('error', 'Enter at least one quantity sold.');
        }

        DB::transaction(function () use ($rows, $validated) {
            foreach ($rows as $row) {
                $product = Product::findOrFail($row['product_id']);

                if ($row['quantity_sold'] > $product->quantity) {
                    abort(422, "Cannot sell more than available stock for {$product->name}.");
                }

                Sale::create([
                    'product_id' => $product->id,
                    'sales_rep_id' => auth()->id(),
                    'quantity_sold' => $row['quantity_sold'],
                    'sale_date' => $validated['sale_date'],
                ]);

                $product->decrement('quantity', $row['quantity_sold']);
            }
        });

        return redirect()->route('sales.create')->with('success', 'Sales sheet submitted.');
    }

    public function index(Request $request)
    {
        $query = Sale::with(['product', 'salesRep'])->latest('sale_date');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('sale_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('sale_date', '<=', $request->to);
        }

        $sales = $query->paginate(20);
        $products = Product::orderBy('name')->get();

        return view('sales.index', compact('sales', 'products'));
    }
}