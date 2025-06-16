<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data yang relevan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $totalRevenue = Order::where('payment_status', 'success')
                            ->sum('total_amount');

        $totalOrders = Order::count();

        $totalProducts = Product::count();

        $totalCustomers = User::where('role', 'customer')->count();

        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        $revenueCurrentYear = Order::where('payment_status', 'success')
                                   ->whereYear('created_at', $currentYear)
                                   ->sum('total_amount');

        $revenuePreviousYear = Order::where('payment_status', 'success')
                                    ->whereYear('created_at', $previousYear)
                                    ->sum('total_amount');

        $growthPercentage = 0;
        if ($revenuePreviousYear > 0) {
            $growthPercentage = (($revenueCurrentYear - $revenuePreviousYear) / $revenuePreviousYear) * 100;
        }

        $latestTransactions = Order::whereIn('payment_status', ['success', 'pending', 'challenge', 'denied'])
                                   ->orderBy('created_at', 'desc')
                                   ->limit(5)
                                   ->get();

        return view('partials.admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'revenueCurrentYear',
            'revenuePreviousYear',
            'growthPercentage',
            'latestTransactions',
            'currentYear',
            'previousYear'
        ));
    }
}
