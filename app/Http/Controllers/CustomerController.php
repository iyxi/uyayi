<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Mail\TransactionCompletedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                    $query->withCount('reviews')
                        ->withAvg('reviews', 'rating')
                        ->orderBy('created_at', 'desc');
                })
                ->paginate($perPage)
                ->appends($request->query());
        } else {
            $products = Product::query()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->appends($request->query());
        }

        return view('homepage', compact('products', 'search'));
    }

    // Shop page view
    public function shop()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('shop', compact('categories'));
    }

    public function collections()
    {
        $categories = Category::active()
            ->with(['products' => function ($query) {
                $query->withCount('reviews')
                    ->withAvg('reviews', 'rating')
                    ->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        return view('collections', compact('categories'));
    }

    public function about()
    {
        return view('about');
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
        $query = Product::query()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

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
            $query->where('category_id', (int) request('category'));
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
        return response()->json(
            Product::query()
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->findOrFail($product->id)
        );
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

        $shippingMethod = $r->input('shipping_method', 'standard');
        $promoCode = strtoupper(trim((string) $r->input('promo_code', '')));
        $promoConfig = $this->availablePromoCodes()[$promoCode] ?? null;
        $order = null;
        $payment = null;
        $total = 0;
        $shippingCost = 0;
        $tax = 0;
        $discount = 0;
        $grandTotal = 0;

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => uniqid('ORD-'),
                'status' => 'completed',
                'shipping_address' => $r->input('shipping_address')
            ]);

            foreach ($normalizedLines as $line) {
                $p = Product::find($line['product_id']);
                if (!$p) {
                    continue;
                }

                $qty = max(1, (int) $line['quantity']);
                $subtotal = $p->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p->id,
                    'quantity' => $qty,
                    'unit_price' => $p->price,
                    'subtotal' => $subtotal
                ]);

                $total += $subtotal;

                // Decrease stock within the same transaction so it rolls back if checkout fails.
                $p->stock = max(0, (int) $p->stock - $qty);
                $p->save();
            }

            if ($total <= 0) {
                DB::rollBack();
                return response()->json(['message' => 'No valid items found in cart'], 400);
            }

            $shippingCost = match ($shippingMethod) {
                'express' => 300,
                'overnight' => 500,
                default => $total >= 1000 ? 0 : 50,
            };

            $tax = round($total * 0.08, 2);

            if ($promoConfig) {
                $discount = match ($promoConfig['type']) {
                    'fixed' => min((float) $promoConfig['value'], $total + $shippingCost + $tax),
                    'percent' => round(($total + $shippingCost + $tax) * ((float) $promoConfig['value'] / 100), 2),
                    default => 0,
                };
            }

            $grandTotal = max(0, round($total + $shippingCost + $tax - $discount, 2));

            $paymentMethod = 'COD';
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'method' => $paymentMethod,
                'amount' => $grandTotal,
                'status' => 'Paid'
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Checkout transaction failed.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to complete the transaction right now. Please try again.'
            ], 500);
        }

        $orderId = $order->id;
        $userEmail = $user->email;

        dispatch(function () use ($orderId, $userEmail) {
            try {
                $orderForMail = Order::query()
                    ->with(['user', 'items.product', 'payment'])
                    ->find($orderId);

                if (!$orderForMail) {
                    return;
                }

                Mail::to($userEmail)->send(new TransactionCompletedMail($orderForMail));
            } catch (\Throwable $e) {
                Log::error('Failed sending transaction completion email.', [
                    'order_id' => $orderId,
                    'error' => $e->getMessage(),
                ]);
            }
        })->afterResponse();

        session()->forget('cart');

        return response()->json([
            'order' => $order->load('items', 'payment'),
            'payment' => $payment,
            'summary' => [
                'subtotal' => round($total, 2),
                'shipping' => round($shippingCost, 2),
                'tax' => round($tax, 2),
                'discount' => round($discount, 2),
                'promo_code' => $promoCode ?: null,
                'total' => $grandTotal,
            ],
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

    private function availablePromoCodes(): array
    {
        return [
            'UYAYI5' => [
                'label' => '5% off your order',
                'type' => 'percent',
                'value' => 5,
            ],
            'BABY10' => [
                'label' => '10% off your order',
                'type' => 'percent',
                'value' => 10,
            ],
            'WELCOME50' => [
                'label' => '₱50 off your order',
                'type' => 'fixed',
                'value' => 50,
            ],
        ];
    }
}
