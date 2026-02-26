<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\User;
use App\Models\Restock;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'recent_orders' => Order::latest()->take(5)->with('user')->get(),
            'low_stock' => Product::whereHas('inventory', function($q) {
                $q->where('stock', '<', 10);
            })->with('inventory')->take(5)->get()
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    // Product CRUD
    public function index()
    {
        $products = Product::with('inventory')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'sku' => 'required|unique:products',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);
        
        $p = Product::create($r->only(['sku','name','description','price','visible']));
        Inventory::create(['product_id'=>$p->id,'stock'=>$r->input('stock',0)]);
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $r, Product $product)
    {
        $product->update($r->only(['name','description','price','visible']));
        if($r->has('stock')){
            $product->inventory()->update(['stock'=>$r->input('stock')]);
        }
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    // Restock
    public function restock(Request $r)
    {
        $pid = $r->input('product_id');
        $qty = (int)$r->input('quantity');
        $inv = Inventory::firstOrCreate(['product_id'=>$pid]);
        $inv->stock += $qty;
        $inv->save();
        // record restock in restocks table (use model or DB)
        \DB::table('restocks')->insert([
            'product_id'=>$pid,
            'added_quantity'=>$qty,
            'restock_date'=>now(),
            'note'=>$r->input('note')
        ]);
        return response()->json(['stock'=>$inv->stock]);
    }

    // Orders
    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])->orderBy('created_at','desc')->paginate(30);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $r, Order $order)
    {
        $order->status = $r->input('status');
        $order->save();
        return response()->json($order);
    }
}
