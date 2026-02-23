<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

class CustomerController extends Controller
{
    public function index()
    {
        return Product::where('visible',1)->with('inventory')->paginate(12);
    }

    public function cart(Request $r)
    {
        $cart = session('cart',[]);
        return response()->json($cart);
    }

    public function addToCart(Request $r, Product $product)
    {
        $qty = max(1,(int)$r->input('quantity',1));
        $cart = session('cart',[]);
        $cart[$product->id] = ['product'=>$product,'quantity'=>$qty];
        session(['cart'=>$cart]);
        return response()->json($cart);
    }

    public function checkout(Request $r)
    {
        $user = $r->user();
        $cart = session('cart',[]);
        if(empty($cart)) return response()->json(['message'=>'Cart empty'],400);
        $order = Order::create([
            'user_id'=>$user->id,
            'order_number'=>uniqid('ORD-'),
            'status'=>'Pending',
            'total'=>0,
            'shipping_address'=>$r->input('shipping_address')
        ]);
        $total = 0;
        foreach($cart as $line){
            $p = Product::find($line['product']->id);
            $qty = $line['quantity'];
            $subtotal = $p->price * $qty;
            OrderItem::create(['order_id'=>$order->id,'product_id'=>$p->id,'quantity'=>$qty,'unit_price'=>$p->price,'subtotal'=>$subtotal]);
            $total += $subtotal;
            // decrease inventory
            $inv = $p->inventory;
            if($inv){
                $inv->stock = max(0,$inv->stock - $qty);
                $inv->save();
            }
        }
        $order->total = $total;
        $order->save();
        // create payment record
        $payment = Payment::create([
            'order_id'=>$order->id,
            'user_id'=>$user->id,
            'method'=>$r->input('method','COD'),
            'amount'=>$total,
            'status'=> $r->input('method') === 'COD' ? 'Pending' : 'Paid'
        ]);
        $order->payment_id = $payment->id;
        $order->save();
        session()->forget('cart');
        return response()->json(['order'=>$order->load('items'),'payment'=>$payment]);
    }

    public function showOrder(Order $order)
    {
        return $order->load('items','payment');
    }
}
