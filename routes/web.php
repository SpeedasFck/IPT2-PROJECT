<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ContactController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('home');
Route::get('/shop', [CatalogController::class, 'index'])->name('shop.index');
Route::get('/products/{product:slug}', [CatalogController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function () {
	Route::get('/login', [AuthController::class, 'createLogin'])->name('login');
	Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
	Route::get('/register', [AuthController::class, 'createRegistration'])->name('register');
	Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/account', fn () => view('account.show', [
	'orders' => request()->user()->orders()->with('items')->latest()->paginate(10),
]))->middleware('auth')->name('account.show');
Route::get('/account/edit', [AccountController::class, 'edit'])->middleware('auth')->name('account.edit');
Route::put('/account', [AccountController::class, 'update'])->middleware('auth')->name('account.update');
Route::put('/account/password', [AccountController::class, 'changePassword'])->middleware('auth')->name('account.password');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{product:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{product:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:8,1')->name('checkout.store');
Route::get('/orders/confirmation/{order:confirmation_token}', [CheckoutController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::view('/terms', 'pages.terms')->name('policies.terms');
Route::view('/privacy', 'pages.privacy')->name('policies.privacy');
Route::view('/returns', 'pages.returns')->name('policies.returns');

Route::prefix('admin')->name('admin.')->middleware(['auth', EnsureUserIsAdmin::class])->group(function () {
	Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
	Route::get('/products', [AdminController::class, 'products'])->name('products.index');
	Route::get('/products/create', [AdminController::class, 'createProduct'])->name('products.create');
	Route::post('/products', [AdminController::class, 'storeProduct'])->name('products.store');
	Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('products.edit');
	Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
	Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
	Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
	Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('categories.update');
	Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
	Route::get('/customers', [AdminController::class, 'customers'])->name('customers.index');
	Route::get('/reports', [AdminController::class, 'reports'])->name('reports.index');
	Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
	Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
});
