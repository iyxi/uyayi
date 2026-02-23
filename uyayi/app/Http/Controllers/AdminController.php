<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    // Route: admin.dashboard
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Route: products.index (from resource)
    public function index()
    {
        $products = Product::all();
        return view('admin.products.index', compact('products'));
    }

    // Route: products.store (from resource)
    public function store(Request $request)
    {
        // Logic to save a new product
    }

    // Route: products.update (from resource)
    public function update(Request $request, $id)
    {
        // Logic to update product
    }

    // Route: products.destroy (from resource)
    public function destroy($id)
    {
        // Logic to delete product
    }

    // Route: inventory.restock
    public function restock(Request $request)
    {
        // Logic to increase stock
    }

    // Route: admin.orders
    public function orders()
    {
        $orders = Order::with('user')->get();
        return view('admin.orders', compact('orders'));
    }

    // Route: admin.orders.status
    public function updateOrderStatus(Request $request, Order $order)
    {
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Order updated!');
    }
}