<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'payment']);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,Processing,Shipped,Completed,Cancelled'
        ]);

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => $request->status]);
            // Add any related updates here (e.g., stock, payment, logs)
        });

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order status updated successfully!');
    }
}