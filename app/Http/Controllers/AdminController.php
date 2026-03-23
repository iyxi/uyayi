<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\OrderStatusUpdatedMail;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller

{
    // Delete product image by path (for edit modal 'x' icon)
    public function deleteImageByPath(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|string',
        ]);
        $imagePath = $request->input('image');
        $images = $product->images ?? [];
        $index = array_search($imagePath, $images);
        if ($index !== false) {
            \Storage::disk('public')->delete($imagePath);
            array_splice($images, $index, 1);
            $product->update(['images' => $images]);
            return response()->json(['success' => true, 'images' => $product->fresh()->images]);
        }
        return response()->json(['success' => false, 'message' => 'Image not found.'], 404);
    }
    public function dashboard()
    {
        $stats = [
            'products' => Product::count(),
            'orders' => Order::count(),
            'users' => User::count(),
            'recent_orders' => Order::latest()->take(5)->with('user')->get(),
            'low_stock' => Product::where('stock', '<', 10)->take(5)->get(),
            'recent_transactions' => Payment::with(['user', 'order'])->latest()->take(10)->get(),
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
            'visible' => 'nullable|boolean',
            'images' => 'nullable|array',
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'visible' => 'nullable|boolean',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $data = [
            'name' => $r->input('name'),
            'description' => $r->input('description'),
            'price' => $r->input('price'),
            'stock' => $r->input('stock'),
            'category_id' => $r->input('category_id'),
            'visible' => $r->has('visible') ? 1 : 0,
        ];
        
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
        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully!',
            'product' => $product->fresh('category'),
        ]);
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
        $rows = [];
        $ext = strtolower($file->getClientOriginalExtension());
        
        if ($ext === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');

            if ($handle === false) {
                return redirect()->route('admin.products.index')->withErrors(['file' => 'Unable to read the uploaded file.']);
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                return redirect()->route('admin.products.index')->withErrors(['file' => 'The uploaded CSV file is empty.']);
            }

            $normalizedHeader = array_map(fn ($h) => $this->normalizeImportHeader($h), $header);

            while (($row = fgetcsv($handle)) !== false) {
                if (!array_filter($row, fn ($value) => trim((string) $value) !== '')) {
                    continue;
                }

                $assoc = [];
                foreach ($normalizedHeader as $i => $key) {
                    if ($key !== '') {
                        $assoc[$key] = $row[$i] ?? null;
                    }
                }
                $rows[] = $assoc;
            }
            fclose($handle);
        } else {
            if ($ext === 'xls' && !class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                return redirect()->route('admin.products.index')->withErrors([
                    'file' => 'Legacy .xls import requires phpoffice/phpspreadsheet. Use .xlsx or .csv, or install the package.'
                ]);
            }

            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
                $spreadsheet = $reader->load($file->getRealPath());
                $worksheet = $spreadsheet->getSheet(0);
                $sheetRows = $worksheet->toArray(null, true, true, false);
            } else {
                $sheetRows = $this->parseXlsxRows($file->getRealPath());
            }

            if (count($sheetRows) === 0) {
                return redirect()->route('admin.products.index')->withErrors(['file' => 'The uploaded worksheet is empty.']);
            }

            $header = array_shift($sheetRows);
            $normalizedHeader = array_map(fn ($h) => $this->normalizeImportHeader($h), $header);

            foreach ($sheetRows as $row) {
                if (!array_filter($row, fn ($value) => trim((string) $value) !== '')) {
                    continue;
                }

                $assoc = [];
                foreach ($normalizedHeader as $i => $key) {
                    if ($key !== '') {
                        $assoc[$key] = $row[$i] ?? null;
                    }
                }

                $rows[] = $assoc;
            }
        }

        if (empty($rows)) {
            return redirect()->route('admin.products.index')->withErrors(['file' => 'No importable rows found in the uploaded file.']);
        }

        if (!isset($rows[0]['name'])) {
            return redirect()->route('admin.products.index')->withErrors([
                'file' => 'Missing required "name" column. Please include a header row.'
            ]);
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if ($name === '') {
                $skipped++;
                continue;
            }

            $sku = trim((string) ($row['sku'] ?? ''));
            if ($sku === '') {
                $sku = 'SKU' . strtoupper(Str::random(8));
            }

            $categoryId = null;
            if (!empty($row['category_id'])) {
                $category = Category::find((int) $row['category_id']);
                $categoryId = $category?->id;
            } elseif (!empty($row['category_name']) || !empty($row['category'])) {
                $categoryName = trim((string) ($row['category_name'] ?? $row['category']));
                if ($categoryName !== '') {
                    $category = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryName)])->first();
                    $categoryId = $category?->id;
                }
            }

            $payload = [
                'name' => $name,
                'description' => $row['description'] ?? null,
                'price' => is_numeric($row['price'] ?? null) ? (float) $row['price'] : 0,
                'stock' => is_numeric($row['stock'] ?? null) ? (int) $row['stock'] : 0,
                'visible' => $this->parseImportVisibleValue($row['visible'] ?? null),
                'category_id' => $categoryId,
            ];

            $product = Product::where('sku', $sku)->first();
            if ($product) {
                $product->update($payload);
                $updated++;
            } else {
                Product::create(array_merge($payload, [
                    'sku' => $sku,
                    'images' => null,
                ]));
                $imported++;
            }
        }

        return redirect()->route('admin.products.index')->with(
            'success',
            "Import completed. Added: {$imported}, Updated: {$updated}, Skipped: {$skipped}."
        );
    }

    public function downloadImportTemplate()
    {
        $headers = [
            'name',
            'sku',
            'description',
            'price',
            'stock',
            'visible',
            'category',
        ];

        $sampleRows = [
            ['Baby Bath', 'SKU-BATH-001', 'Gentle baby bath wash', '130.00', '40', 'yes', 'Bath Essentials'],
            ['Baby Lotion', 'SKU-LOTION-001', 'Moisturizing lotion for infants', '145.00', '30', 'yes', 'Skin Care'],
        ];

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="products-import-template.csv"',
        ]);
    }

    private function normalizeImportHeader($header): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]+/', '_', (string) $header), '_'));
    }

    private function parseImportVisibleValue($value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'y', 'active'], true);
    }

    private function parseXlsxRows(string $xlsxPath): array
    {
        $zip = new \ZipArchive();
        if ($zip->open($xlsxPath) !== true) {
            return [];
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return [];
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sharedDoc = @simplexml_load_string($sharedXml);
            if ($sharedDoc) {
                foreach ($sharedDoc->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string) $si->t;
                        continue;
                    }

                    $parts = [];
                    if (isset($si->r)) {
                        foreach ($si->r as $run) {
                            $parts[] = (string) ($run->t ?? '');
                        }
                    }
                    $sharedStrings[] = implode('', $parts);
                }
            }
        }

        $sheetDoc = @simplexml_load_string($sheetXml);
        $zip->close();

        if (!$sheetDoc || !isset($sheetDoc->sheetData)) {
            return [];
        }

        $rows = [];
        foreach ($sheetDoc->sheetData->row as $rowNode) {
            $row = [];
            foreach ($rowNode->c as $cell) {
                $cellRef = (string) ($cell['r'] ?? '');
                $columnIndex = $this->xlsxColumnIndexFromCellRef($cellRef);

                $type = (string) ($cell['t'] ?? '');
                $value = '';

                if ($type === 'inlineStr') {
                    $value = (string) ($cell->is->t ?? '');
                } elseif ($type === 's') {
                    $sharedIndex = (int) ($cell->v ?? -1);
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } else {
                    $value = (string) ($cell->v ?? '');
                }

                if ($columnIndex >= 0) {
                    $row[$columnIndex] = $value;
                }
            }

            if (!empty($row)) {
                ksort($row);
                $rows[] = array_values($row);
            }
        }

        return $rows;
    }

    private function xlsxColumnIndexFromCellRef(string $cellRef): int
    {
        if ($cellRef === '') {
            return -1;
        }

        if (!preg_match('/^([A-Z]+)\d+$/', strtoupper($cellRef), $matches)) {
            return -1;
        }

        $letters = $matches[1];
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - ord('A') + 1);
        }

        return $index - 1;
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
        $validated = $r->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'supplier' => 'required|string|max:255',
            'unit_cost' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $qty = (int) $validated['quantity'];
        $supplier = trim((string) $validated['supplier']);
        $unitCost = (float) $validated['unit_cost'];
        $expenseAmount = round($qty * $unitCost, 2);

        $product->stock += $qty;
        $product->save();

        if (Schema::hasTable('restocks')) {
            DB::table('restocks')->insert([
                'product_id' => $product->id,
                'added_quantity' => $qty,
                'restock_date' => now(),
                'note' => $validated['note'] ?? null,
            ]);
        }

        Expense::create([
            'description' => 'Restock purchase: ' . $product->name . ' x' . $qty . ' from ' . $supplier . (!empty($validated['note']) ? ' (' . $validated['note'] . ')' : ''),
            'amount' => $expenseAmount,
            'expense_date' => now()->toDateString(),
        ]);
        
        if($r->wantsJson()) {
            return response()->json([
                'stock' => $product->stock,
                'expense_amount' => $expenseAmount,
            ]);
        }
        
        return redirect()->back()->with('success', 'Stock updated and supplier expense recorded successfully!');
    }

    // Orders
    public function orders()
    {
        $orders = Order::with(['user', 'items.product'])->orderBy('created_at','desc')->paginate(30);
        return view('admin.orders.index', compact('orders'));
    }

    public function updateOrderStatus(Request $r, Order $order)
    {
        $validated = $r->validate([
            'status' => ['required', 'in:pending,processing,shipped,completed,cancelled'],
        ]);

        $order->status = $validated['status'];
        $order->save();

        try {
            $orderForMail = $order->fresh()->load(['user', 'items.product', 'payment']);
            if ($orderForMail->user && $orderForMail->user->email) {
                Mail::to($orderForMail->user->email)->send(new OrderStatusUpdatedMail($orderForMail));
            }
        } catch (\Throwable $e) {
            Log::error('Failed sending order status update email.', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }

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

    public function reviews()
    {
        $reviews = Review::with(['user', 'reviewable'])
            ->latest()
            ->paginate(20);

        $stats = [
            'count' => Review::count(),
            'average' => round((float) Review::avg('rating'), 2),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function destroyReview(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews')->with('success', 'Review deleted successfully!');
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

    // Delete product image by path (for edit modal 'x' icon)
}
