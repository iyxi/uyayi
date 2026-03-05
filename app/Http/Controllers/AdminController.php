<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'recent_orders' => Order::latest()->take(5)->with('user')->get(),
            'low_stock' => Product::where('stock', '<', 10)->take(5)->get()
        ];
        
        return view('admin.dashboard', compact('stats'));
    }

    // Product CRUD
    public function index()
    {
        $products = Product::paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0'
        ]);
        
        // Auto-generate SKU
        $sku = 'SKU' . strtoupper(Str::random(8));
        
        $p = Product::create([
            'sku' => $sku,
            'name' => $r->input('name'),
            'description' => $r->input('description'),
            'price' => $r->input('price'),
            'stock' => $r->input('stock', 0),
            'visible' => $r->has('visible') ? 1 : 0
        ]);
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $r, Product $product)
    {
        $product->update($r->only(['name','description','price','stock','visible']));
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    // Customers
    public function customers()
    {
        $customers = User::withCount('orders')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('admin.customers.index', compact('customers'));
    }

    // Inventory / Stock Management
    public function inventory()
    {
        $products = Product::paginate(20);
        return view('admin.inventory.index', compact('products'));
    }

    public function restock(Request $r)
    {
        $product = Product::findOrFail($r->input('product_id'));
        $qty = (int)$r->input('quantity');
        $product->stock += $qty;
        $product->save();
        
        if($r->wantsJson()) {
            return response()->json(['stock'=>$product->stock]);
        }
        
        return redirect()->back()->with('success', 'Stock updated successfully!');
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

    // Payments
    public function payments()
    {
        $payments = Payment::with(['order', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total' => Payment::where('status', 'Paid')->sum('amount'),
            'pending' => Payment::where('status', 'Pending')->sum('amount'),
            'count' => Payment::count()
        ];
        
        return view('admin.payments.index', compact('payments', 'stats'));
    }

    // Expenses
    public function expenses()
    {
        $expenses = Expense::orderBy('expense_date', 'desc')->paginate(20);
        
        $stats = [
            'total' => Expense::sum('amount'),
            'this_month' => Expense::whereMonth('expense_date', now()->month)
                ->whereYear('expense_date', now()->year)
                ->sum('amount'),
            'count' => Expense::count()
        ];
        
        return view('admin.expenses.index', compact('expenses', 'stats'));
    }

    public function storeExpense(Request $r)
    {
        $r->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date'
        ]);
        
        Expense::create($r->only(['description', 'amount', 'expense_date']));
        
        return redirect()->route('admin.expenses')->with('success', 'Expense added successfully!');
    }

    public function destroyExpense($id)
    {
        Expense::findOrFail($id)->delete();
        return redirect()->route('admin.expenses')->with('success', 'Expense deleted!');
    }

    // Reports
    public function reports()
    {
        $salesByMonth = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total) as total, COUNT(*) as count')
            ->where('status', '!=', 'Cancelled')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('YEAR(created_at) DESC, MONTH(created_at) DESC')
            ->take(12)
            ->get();
        
        $topProducts = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();
        
        $stats = [
            'total_sales' => Order::where('status', '!=', 'Cancelled')->sum('total'),
            'total_orders' => Order::count(),
            'total_customers' => User::count(),
            'total_products' => Product::count()
        ];
        
        return view('admin.reports.index', compact('salesByMonth', 'topProducts', 'stats'));
    }

    // Settings
    public function settings()
    {
        return view('admin.settings.index');
    }

    // Account
    public function account()
    {
        return view('admin.account.index');
    }

    public function updateAccount(Request $r)
    {
        $user = Auth::user();
        
        $r->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed'
        ]);
        
        $user->name = $r->name;
        $user->email = $r->email;
        
        if ($r->filled('new_password')) {
            if (!Hash::check($r->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }
            $user->password = Hash::make($r->new_password);
        }
        
        $user->save();
        
        return redirect()->route('admin.account')->with('success', 'Account updated successfully!');
    }
}
