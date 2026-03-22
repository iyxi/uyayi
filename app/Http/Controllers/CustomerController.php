<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;

class CustomerController extends Controller
{
    // Homepage view
    public function homepage(Request $request)
    {
        $search = trim((string) $request->query('search', ''));
        $perPage = 12;

        if ($search !== '') {
            $products = Product::search($search)
                ->query(function ($query) {
                    $query->orderBy('created_at', 'desc');
                })
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            $products = Product::query()
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        }

        return view('homepage', compact('products', 'search'));
    }

    // Shop page view
    public function shop()
    {
        return view('shop');
    }

    // Product details page
    public function product(Product $product)
    {
        return view('product-details', compact('product'));
    }

    // Cart view page
    public function cartView()
    {
        return view('cart');
    }

    // Checkout page
    public function checkoutPage()
    {
        return view('checkout');
    }

    public function myOrders(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'payment'])
            ->latest()
            ->paginate(10);

        return view('my-orders', compact('orders'));
    }

    // API endpoint for products (used by frontend)
    public function index()
    {
        $query = Product::query();

        // Search filter
        if (request()->filled('search')) {
            $search = trim(request('search'));
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%");
            });
        }

        // Category filter
        if (request()->filled('category')) {
            $query->whereHas('category', function($q) {
                $q->where('name', request('category'));
            });
        }

        // Price filter (expects format: min-max or min+)
        if (request()->filled('price')) {
            $price = request('price');
            if (strpos($price, '-') !== false) {
                [$min, $max] = explode('-', $price);
                $query->whereBetween('price', [(float)$min * 10, (float)$max * 10]);
            } elseif (strpos($price, '+') !== false) {
                $min = (float)str_replace('+', '', $price);
                $query->where('price', '>=', $min * 10);
            }
        }

        // Sorting
        $sort = request('sort', 'name');
        if ($sort === 'price-low') {
            $query->orderBy('price', 'asc');
        } elseif ($sort === 'price-high') {
            $query->orderBy('price', 'desc');
        } elseif ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy('name');
        }

        return $query->paginate(12);
    }

    public function showProductApi(Product $product)
    {
        return response()->json($product);
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

        // Support both session cart and cart payload from frontend localStorage.
        $cart = session('cart', []);
        $incomingItems = $r->input('cart_items', []);

        if (empty($cart) && !is_array($incomingItems)) {
            return response()->json(['message' => 'Cart empty'], 400);
        }

        $normalizedLines = [];

        if (!empty($cart)) {
            foreach ($cart as $line) {
                $productId = data_get($line, 'product.id') ?? data_get($line, 'product_id');
                $qty = max(1, (int) data_get($line, 'quantity', 1));
                if ($productId) {
                    $normalizedLines[] = ['product_id' => (int) $productId, 'quantity' => $qty];
                }
            }
        } else {
            foreach ($incomingItems as $line) {
                $productId = data_get($line, 'product_id') ?? data_get($line, 'product.id');
                $qty = max(1, (int) data_get($line, 'quantity', 1));
                if ($productId) {
                    $normalizedLines[] = ['product_id' => (int) $productId, 'quantity' => $qty];
                }
            }
        }

        if (empty($normalizedLines)) {
            return response()->json(['message' => 'Cart empty'], 400);
        }

        $order = Order::create([
            'user_id'=>$user->id,
            'order_number'=>uniqid('ORD-'),
            'status'=>'Pending',
            'total'=>0,
            'shipping_address'=>$r->input('shipping_address')
        ]);
        $total = 0;
        foreach($normalizedLines as $line){
            $p = Product::find($line['product_id']);
            if (!$p) {
                continue;
            }

            $qty = max(1, (int) $line['quantity']);
            $subtotal = $p->price * $qty;
            OrderItem::create(['order_id'=>$order->id,'product_id'=>$p->id,'quantity'=>$qty,'unit_price'=>$p->price,'subtotal'=>$subtotal]);
            $total += $subtotal;
            // decrease product stock directly
            $p->stock = max(0, (int)$p->stock - $qty);
            $p->save();
        }

        if ($total <= 0) {
            $order->delete();
            return response()->json(['message' => 'No valid items found in cart'], 400);
        }

        $order->total = $total;
        $order->save();

        $paymentMethod = 'COD';
        // create payment record
        $payment = Payment::create([
            'order_id'=>$order->id,
            'user_id'=>$user->id,
            'method'=>$paymentMethod,
            'amount'=>$total,
            'status'=> 'Paid'
        ]);
        session()->forget('cart');

        return response()->json([
            'order' => $order->load('items', 'payment'),
            'payment' => $payment,
            'redirect_url' => route('orders.index'),
        ]);
    }

    public function showOrder(Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            abort(403);
        }

        return $order->load('items','payment');
    }
}
