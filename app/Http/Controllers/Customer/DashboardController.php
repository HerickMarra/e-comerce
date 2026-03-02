<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->latest()->get();

        $totalOrders = $orders->count();
        $inTransit = $orders->whereIn('status', ['shipped', 'processing'])->count();
        $recentOrders = $orders->take(4);

        return view('customer.dashboard.index', compact('totalOrders', 'inTransit', 'recentOrders'));
    }

    public function orders(Request $request)
    {
        $query = Order::where('user_id', Auth::id())
            ->with('items.product')
            ->latest();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where('order_number', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    public function showOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product.images');

        return view('customer.orders.show', compact('order'));
    }

    public function profile()
    {
        return view('customer.profile.edit');
    }

    public function updateProfile(\App\Http\Requests\ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return back()->with('status', 'profile-updated');
    }
}
