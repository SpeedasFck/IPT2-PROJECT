<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('admin.dashboard', [
            'productCount' => Product::count(),
            'orderCount' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'completedCount' => Order::where('status', 'delivered')->count(),
            'customerCount' => \App\Models\User::where('is_admin', false)->count(),
            'lowStockCount' => Product::whereColumn('stock', '<=', 'low_stock_threshold')->count(),
            'salesTotal' => Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total'),
            'recentOrders' => Order::latest()->take(8)->get(),
        ]);
    }

    public function products(): View
    {
        return view('admin.products.index', ['products' => Product::with('category')->orderBy('name')->paginate(20)]);
    }

    public function createProduct(): View
    {
        return view('admin.products.form', ['product' => new Product(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function storeProduct(Request $request): RedirectResponse
    {
        $data = $this->validateProduct($request);
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['image_url'] = $request->file('image')?->store('products', 'public');
        Product::create($data);

        return redirect()->route('admin.products.index')->with('status', 'Product created.');
    }

    public function editProduct(Product $product): View
    {
        return view('admin.products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validateProduct($request, $product);
        $data['slug'] = $this->uniqueSlug($data['name'], $product->id);
        $data['is_active'] = $request->boolean('is_active');
        $data['image_url'] = $request->file('image')?->store('products', 'public') ?: $product->image_url;
        $product->update($data);

        return redirect()->route('admin.products.index')->with('status', 'Product updated.');
    }

    public function categories(): View
    {
        return view('admin.categories.index', ['categories' => Category::withCount('products')->orderBy('name')->get()]);
    }

    public function storeCategory(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', 'unique:categories,name'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = true;
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category created.');
    }

    public function updateCategory(Request $request, Category $category): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120', Rule::unique('categories', 'name')->ignore($category->id)],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);
        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('status', 'Category updated.');
    }

    public function orders(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])],
        ]);
        $orders = Order::query()->latest();

        if (! empty($filters['q'])) {
            $orders->where(fn ($query) => $query->where('order_number', 'like', '%' . $filters['q'] . '%')
                ->orWhere('customer_name', 'like', '%' . $filters['q'] . '%')
                ->orWhere('email', 'like', '%' . $filters['q'] . '%'));
        }

        if (! empty($filters['status'])) {
            $orders->where('status', $filters['status']);
        }

        return view('admin.orders.index', ['orders' => $orders->paginate(20)->withQueryString(), 'filters' => $filters]);
    }

    public function showOrder(Order $order): View
    {
        return view('admin.orders.show', ['order' => $order->load('items', 'user')]);
    }

    public function customers(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $customers = User::query()->where('is_admin', false)->withCount('orders')->latest();

        if (! empty($filters['q'])) {
            $customers->where(fn ($query) => $query->where('name', 'like', '%' . $filters['q'] . '%')
                ->orWhere('email', 'like', '%' . $filters['q'] . '%'));
        }

        return view('admin.customers.index', ['customers' => $customers->paginate(20)->withQueryString(), 'filters' => $filters]);
    }

    public function reports(Request $request): View
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $orders = Order::query()->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered']);

        if (! empty($filters['from'])) {
            $orders->whereDate('created_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $orders->whereDate('created_at', '<=', $filters['to']);
        }

        $orderIds = (clone $orders)->select('id');
        $sales = (clone $orders)->sum('total');
        $dailySales = (clone $orders)->selectRaw('DATE(created_at) as sales_date, SUM(total) as sales_total, COUNT(*) as order_count')
            ->groupByRaw('DATE(created_at)')
            ->orderByDesc('sales_date')
            ->limit(31)
            ->get();
        $bestSellers = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereIn('orders.id', $orderIds)
            ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as units_sold'), DB::raw('SUM(order_items.line_total) as revenue'))
            ->groupBy('order_items.product_name')
            ->orderByDesc('units_sold')
            ->limit(10)
            ->get();

        return view('admin.reports.index', [
            'filters' => $filters,
            'sales' => $sales,
            'dailySales' => $dailySales,
            'bestSellers' => $bestSellers,
            'lowStock' => Product::with('category')->whereColumn('stock', '<=', 'low_stock_threshold')->orderBy('stock')->limit(30)->get(),
            'orderStatuses' => Order::select('status', DB::raw('COUNT(*) as order_count'))->groupBy('status')->get(),
        ]);
    }

    public function updateOrderStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])]]);

        try {
            DB::transaction(function () use ($order, $validated) {
                $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                $next = $validated['status'];
                $transitions = [
                    'pending' => ['confirmed', 'cancelled'],
                    'confirmed' => ['processing', 'cancelled'],
                    'processing' => ['shipped', 'cancelled'],
                    'shipped' => ['delivered'],
                    'delivered' => [],
                    'cancelled' => [],
                ];

                if (! in_array($next, $transitions[$lockedOrder->status] ?? [], true)) {
                    throw new RuntimeException('That order status transition is not allowed.');
                }

                if ($next === 'cancelled') {
                    foreach ($lockedOrder->items as $item) {
                        if ($item->product_id) {
                            Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                        }
                    }
                }

                $lockedOrder->update(['status' => $next]);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        } catch (QueryException) {
            return back()->withErrors(['status' => 'The order could not be updated. Please try again.']);
        }

        return back()->with('status', 'Order status updated.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:10000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sku' => ['nullable', 'string', 'max:80', Rule::unique('products', 'sku')->ignore($product?->id)],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:10000000'],
            'low_stock_threshold' => ['required', 'integer', 'min:0', 'max:10000000'],
            'image' => [$product ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $exceptId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $suffix = 2;

        while (Product::where('slug', $slug)->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }
}