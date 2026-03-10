<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Category;
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
        $products = Product::with('category')->paginate(20);
        $categories = Category::active()->orderBy('name')->get();
        $trashedCount = Product::onlyTrashed()->count();
        return view('admin.products.index', compact('products', 'categories', 'trashedCount'));
    }

    public function trashed()
    {
        $products = Product::onlyTrashed()->with('category')->paginate(20);
        return view('admin.products.trashed', compact('products'));
    }

    public function store(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        
        // Auto-generate SKU
        $sku = 'SKU' . strtoupper(Str::random(8));
        
        // Handle image uploads
        $imagePaths = [];
        if ($r->hasFile('images')) {
            foreach ($r->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
        }
        
        $p = Product::create([
            'sku' => $sku,
            'name' => $r->input('name'),
            'description' => $r->input('description'),
            'price' => $r->input('price'),
            'stock' => $r->input('stock', 0),
            'category_id' => $r->input('category_id'),
            'visible' => $r->has('visible') ? 1 : 0,
            'images' => $imagePaths
        ]);
        
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function update(Request $r, Product $product)
    {
        $r->validate([
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = $r->only(['name','description','price','stock','visible','category_id']);
        
        // Handle new image uploads
        if ($r->hasFile('images')) {
            $imagePaths = $product->images ?? [];
            foreach ($r->file('images') as $image) {
                $path = $image->store('products', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }
        
        $product->update($data);
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();
        return redirect()->route('admin.products.trashed')->with('success', 'Product restored successfully!');
    }

    public function forceDelete($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        // Delete associated images from storage
        if ($product->images) {
            foreach ($product->images as $imagePath) {
                \Storage::disk('public')->delete($imagePath);
            }
        }
        $product->forceDelete();
        return redirect()->route('admin.products.trashed')->with('success', 'Product permanently deleted!');
    }

    public function deleteImage(Product $product, $index)
    {
        $images = $product->images ?? [];
        if (isset($images[$index])) {
            \Storage::disk('public')->delete($images[$index]);
            array_splice($images, $index, 1);
            $product->update(['images' => $images]);
        }
        return response()->json(['success' => true, 'images' => $product->fresh()->images]);
    }

    public function importProducts(Request $r)
    {
        $r->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        $file = $r->file('file');
        $data = [];
        
        if ($file->getClientOriginalExtension() === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = fgetcsv($handle);
            while (($row = fgetcsv($handle)) !== false) {
                $data[] = array_combine($header, $row);
            }
            fclose($handle);
        } else {
            // For Excel files, use PhpSpreadsheet if available, otherwise CSV only
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            $header = array_shift($rows);
            foreach ($rows as $row) {
                if (array_filter($row)) {
                    $data[] = array_combine($header, $row);
                }
            }
        }

        $imported = 0;
        foreach ($data as $row) {
            if (empty($row['name'])) continue;
            
            Product::create([
                'sku' => $row['sku'] ?? 'SKU' . strtoupper(Str::random(8)),
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'price' => floatval($row['price'] ?? 0),
                'stock' => intval($row['stock'] ?? 0),
                'visible' => isset($row['visible']) ? (strtolower($row['visible']) === 'yes' || $row['visible'] == 1) : true,
                'images' => null
            ]);
            $imported++;
        }

        return redirect()->route('admin.products.index')->with('success', "Imported {$imported} products successfully!");
    }

    public function show(Product $product)
    {
        $product->load('category');
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

    public function restocks()
    {
        $restocks = \DB::table('restocks')
            ->join('products', 'restocks.product_id', '=', 'products.id')
            ->select('restocks.*', 'products.name as product_name', 'products.sku')
            ->orderBy('restocks.restock_date', 'desc')
            ->paginate(20);
        
        return view('admin.inventory.restocks', compact('restocks'));
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

    // Categories Management
    public function categories()
    {
        $categories = Category::withCount('products')->paginate(15);
        return view('admin.categories.index', compact('categories'));
    }

    public function storeCategory(Request $r)
    {
        $r->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Category::create([
            'name' => $r->name,
            'description' => $r->description,
            'is_active' => $r->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $r, Category $category)
    {
        $r->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update([
            'name' => $r->name,
            'description' => $r->description,
            'is_active' => $r->has('is_active') ? 1 : 0
        ]);

        return response()->json($category);
    }

    public function destroyCategory(Category $category)
    {
        if ($category->products()->count() > 0) {
            return response()->json(['error' => 'Cannot delete category with products. Please reassign products first.'], 422);
        }
        
        $category->delete();
        return response()->noContent();
    }

    // User Management
    public function users()
    {
        $users = User::withCount('orders')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function showUser(User $user)
    {
        $user->load('orders');
        return response()->json($user);
    }

    public function updateUserStatus(Request $r, User $user)
    {
        $r->validate([
            'status' => 'required|in:active,inactive'
        ]);

        $user->update(['status' => $r->status]);
        return response()->json(['success' => true, 'message' => 'User status updated successfully']);
    }

    public function updateUserRole(Request $r, User $user)
    {
        $r->validate([
            'role' => 'required|in:customer,admin'
        ]);

        $user->update(['role' => $r->role]);
        return response()->json(['success' => true, 'message' => 'User role updated successfully']);
    }
}
