<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Restock;

class AdminController extends Controller
{
    public function dashboard()
    {
        $products = Product::count();
        $orders = Order::count();
        return response()->json(['products'=>$products,'orders'=>$orders]);
    }

    // Product CRUD
    public function index()
    {
        return Product::with('inventory')->paginate(20);
    }

    public function store(Request $r)
    {
        $p = Product::create($r->only(['sku','name','description','price','visible']));
        Inventory::create(['product_id'=>$p->id,'stock'=>$r->input('stock',0)]);
        return response()->json($p,201);
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
        return Order::with('items')->orderBy('created_at','desc')->paginate(30);
    }

    public function updateOrderStatus(Request $r, Order $order)
    {
        $order->status = $r->input('status');
        $order->save();
        return response()->json($order);
    }
}
