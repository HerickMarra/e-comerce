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

        // Active Customers: Exclude Admins
        $totalCustomers = User::whereDoesntHave('roles', function ($q) {
            $q->where('name', 'Admin');
        })->count();

        // Recent Orders
        $recentOrders = Order::with('user')->latest()->take(10)->get();

        // Chart Data (Last 30 days)
        $days = [];
        $ordersMadeCounts = []; // Total orders (including pending/cancelled)
        $ordersPaidCounts = []; // Success orders only
        $revenueData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $days[] = Carbon::now()->subDays($i)->format('d/m');

            // All orders count
            $ordersMadeCounts[] = Order::whereDate('created_at', $date)->count();

            // Paid orders count & Revenue
            $dayPaidData = Order::whereIn('status', $successStatuses)
                ->whereDate('created_at', $date)
                ->select(DB::raw('count(*) as count'), DB::raw('sum(total_amount) as revenue'))
                ->first();

            $ordersPaidCounts[] = $dayPaidData->count ?? 0;
            $revenueData[] = $dayPaidData->revenue ?? 0;
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
