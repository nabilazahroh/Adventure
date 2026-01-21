<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard view.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        
        $todaySales = Sale::whereDate('transaction_date', Carbon::today())->sum('total');
        
        $lowStockCount = Product::where('stock', '<=', 5)->count();
        
        $lowStockProducts = Product::where('stock', '<=', 5)
            ->orderBy('stock', 'asc')
            ->get();
        
        $topProducts = Product::select('products.*')
            ->selectRaw('COALESCE(SUM(sales.quantity), 0) as total_sold')
            ->leftJoin('sales', 'products.id', '=', 'sales.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        $recentActivities = Sale::with('product', 'user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($sale) {
                return [
                    'icon' => 'fa-shopping-cart',
                    'title' => 'Transaksi Penjualan',
                    'description' => "{$sale->user->name} menjual {$sale->quantity} {$sale->product->name}",
                    'time' => $sale->created_at->diffForHumans()
                ];
            });
        
        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'todaySales' => $todaySales,
            'lowStockCount' => $lowStockCount,
            'lowStockProducts' => $lowStockProducts,
            'recentActivities' => $recentActivities,
            'topProducts' => $topProducts
        ]);
    }
}
