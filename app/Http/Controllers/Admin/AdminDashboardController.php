<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $successStatuses = ['paid', 'processing', 'shipped', 'delivered'];
        $totalRevenue = Order::whereIn('status', $successStatuses)->sum('total_amount');
        $totalOrders = Order::whereIn('status', $successStatuses)->count();

        // Active Customers: Exclude Admins & Managers
        $totalCustomers = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['Super Admin', 'Admin', 'Manager']);
        })->count();

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Chart Data (Last 30 days) - Single aggregated query instead of 60 individual queries in loop
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();

        $dailyStats = Order::where('created_at', '>=', $thirtyDaysAgo)
            ->selectRaw("
                DATE(created_at) as order_date,
                COUNT(*) as total_count,
                SUM(CASE WHEN status IN ('paid', 'processing', 'shipped', 'delivered') THEN 1 ELSE 0 END) as paid_count,
                SUM(CASE WHEN status IN ('paid', 'processing', 'shipped', 'delivered') THEN total_amount ELSE 0 END) as daily_revenue
            ")
            ->groupBy('order_date')
            ->get()
            ->keyBy('order_date');

        $days = [];
        $ordersMadeCounts = [];
        $ordersPaidCounts = [];
        $revenueData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = Carbon::now()->subDays($i)->format('d/m');

            $stat = $dailyStats->get($date);
            $ordersMadeCounts[] = (int) ($stat->total_count ?? 0);
            $ordersPaidCounts[] = (int) ($stat->paid_count ?? 0);
            $revenueData[] = (float) ($stat->daily_revenue ?? 0);
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'totalCustomers',
            'recentOrders',
            'days',
            'ordersMadeCounts',
            'ordersPaidCounts',
            'revenueData'
        ));
    }
}
