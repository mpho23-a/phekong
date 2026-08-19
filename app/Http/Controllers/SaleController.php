<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{

public function dashboard()
{
    $today = Carbon::today();
    $startOfWeek = $today->copy()->startOfWeek();
    $startOfMonth = $today->copy()->startOfMonth();
    $startOfYear = $today->copy()->startOfYear();

    $summarize = function ($from) {
        $sales = Sale::whereDate('sale_date', '>=', $from)->get();
        return [
            'revenue' => $sales->sum(fn ($s) => $s->quantity_sold * $s->price_at_sale),
            'units' => $sales->sum('quantity_sold'),
            'transactions' => $sales->count(),
        ];
    };

    $todayStats = $summarize($today);
    $weekStats = $summarize($startOfWeek);
    $monthStats = $summarize($startOfMonth);
    $yearStats = $summarize($startOfYear);

    // Last 7 days revenue, for the chart
    $last7Days = collect(range(6, 0))->map(function ($daysAgo) {
        $date = Carbon::today()->subDays($daysAgo);
        $revenue = Sale::whereDate('sale_date', $date)
            ->get()
            ->sum(fn ($s) => $s->quantity_sold * $s->price_at_sale);

        return [
            'label' => $date->format('D'),
            'revenue' => round($revenue, 2),
        ];
    });

    // Top products this month, by revenue
    $topProducts = Sale::whereDate('sale_date', '>=', $startOfMonth)
        ->get()
        ->groupBy('product_id')
        ->map(function ($sales) {
            return [
                'name' => $sales->first()->product->name,
                'units' => $sales->sum('quantity_sold'),
                'revenue' => $sales->sum(fn ($s) => $s->quantity_sold * $s->price_at_sale),
            ];
        })
        ->sortByDesc('revenue')
        ->take(5)
        ->values();

    return view('sales.dashboard', compact('todayStats', 'weekStats', 'monthStats', 'yearStats', 'last7Days', 'topProducts'));
}

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
                'price_at_sale' => $product->price,
                'sale_date' => $validated['sale_date'],
            ]);

            $product->decrement('quantity', $row['quantity_sold']);
        }
    });

    return redirect()->back()->with('success', 'Sales sheet submitted.');
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