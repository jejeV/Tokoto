<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;
        $currentMonth = Carbon::now()->month;
        $currentDate = Carbon::now()->toDateString();

        // 1. Key Metrics
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 'customer')->count();

        // Today's metrics
        $todayRevenue = Order::where('payment_status', 'paid')
                            ->whereDate('created_at', $currentDate)
                            ->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', $currentDate)->count();

        // Monthly metrics
        $monthlyRevenue = Order::where('payment_status', 'paid')
                              ->whereYear('created_at', $currentYear)
                              ->whereMonth('created_at', $currentMonth)
                              ->sum('total_amount');
        $monthlyOrders = Order::whereYear('created_at', $currentYear)
                            ->whereMonth('created_at', $currentMonth)
                            ->count();

        // 2. Order Status Statistics (like in OrderController)
        $orderStatistics = [
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        // 3. Revenue Comparison
        $revenueCurrentYear = Order::where('payment_status', 'paid')
                                 ->whereYear('created_at', $currentYear)
                                 ->sum('total_amount');
        $revenuePreviousYear = Order::where('payment_status', 'paid')
                                  ->whereYear('created_at', $previousYear)
                                  ->sum('total_amount');

        $growthPercentage = $this->calculateGrowthPercentage($revenueCurrentYear, $revenuePreviousYear);

        // 4. Monthly Revenue Data
        $monthlyRevenueCurrentYear = $this->getMonthlyRevenueData($currentYear);
        $monthlyRevenuePreviousYear = $this->getMonthlyRevenueData($previousYear);

        // 6. Top Selling Products
        $topProducts = $this->getTopSellingProducts();

        // 7. Order Status Distribution
        $orderStatuses = $this->getOrderStatusDistribution();

        $recentOrders = Order::with(['customer', 'orderItems'])
                           ->latest()
                           ->take(8)
                           ->get();

        // 9. Recent Customers
        $recentCustomers = User::where('role', 'customer')
                             ->latest()
                             ->take(5)
                             ->get();

        return view('partials.admin.dashboard', compact(
            'totalRevenue', 'totalOrders', 'totalProducts', 'totalCustomers',
            'todayRevenue', 'todayOrders', 'monthlyRevenue', 'monthlyOrders',
            'orderStatistics', 'revenueCurrentYear', 'revenuePreviousYear', 'growthPercentage',
            'monthlyRevenueCurrentYear', 'monthlyRevenuePreviousYear',
            'topProducts', 'orderStatuses',
            'recentOrders', 'recentCustomers',
            'currentYear', 'previousYear'
        ));
    }

    protected function calculateGrowthPercentage($current, $previous)
    {
        if ($previous > 0) {
            return (($current - $previous) / $previous) * 100;
        } elseif ($current > 0) {
            return 100;
        }
        return 0;
    }

    protected function getMonthlyRevenueData($year)
    {
        $monthlyData = Order::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('payment_status', 'paid')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $formattedData = [];
        for ($i = 1; $i <= 12; $i++) {
            $formattedData[] = $monthlyData[$i] ?? 0;
        }

        return $formattedData;
    }

    protected function getTopSellingProducts()
    {
        return Product::withSum(['orderItems' => function($query) {
            $query->select(DB::raw('SUM(quantity) as total_sold'))
                  ->join('orders', 'order_items.order_id', '=', 'orders.id')
                  ->where('orders.payment_status', 'paid');
        }], 'quantity')
        ->orderBy('order_items_sum_quantity', 'desc')
        ->limit(5)
        ->get();
    }

    protected function getOrderStatusDistribution()
    {
        return Order::select('order_status', DB::raw('COUNT(*) as count'))
                   ->groupBy('order_status')
                   ->get();
    }
}
