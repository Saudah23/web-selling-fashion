<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Owner\OwnerDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\Customer\WishlistController;
use App\Http\Controllers\Customer\CartController;

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/password/reset', [AuthController::class, 'showPasswordRequestForm'])->name('password.request');

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Address management routes (customer only)
    Route::middleware('role:customer')->group(function () {
        Route::get('/addresses', [App\Http\Controllers\Customer\AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [App\Http\Controllers\Customer\AddressController::class, 'store'])->name('addresses.store');
        Route::get('/addresses/{address}', [App\Http\Controllers\Customer\AddressController::class, 'show'])->name('addresses.show');
        Route::get('/addresses/{address}/edit', [App\Http\Controllers\Customer\AddressController::class, 'edit'])->name('addresses.edit');
        Route::put('/addresses/{address}', [App\Http\Controllers\Customer\AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{address}', [App\Http\Controllers\Customer\AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{address}/default', [App\Http\Controllers\Customer\AddressController::class, 'makeDefault'])->name('addresses.default');
    });

    // Wishlist routes (authenticated users only)
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/check', [WishlistController::class, 'check'])->name('wishlist.check');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    // Cart routes (authenticated users only)
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout routes (authenticated users only)
    Route::prefix('checkout')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/calculate-shipping', [App\Http\Controllers\Customer\CheckoutController::class, 'calculateShipping'])->name('checkout.shipping');
        Route::post('/process', [App\Http\Controllers\Customer\CheckoutController::class, 'process'])->name('checkout.process');
        Route::get('/payment/{order}', [App\Http\Controllers\Customer\CheckoutController::class, 'payment'])->name('checkout.payment');
        Route::get('/success', [App\Http\Controllers\Customer\CheckoutController::class, 'success'])->name('checkout.success');
        Route::get('/pending', [App\Http\Controllers\Customer\CheckoutController::class, 'pending'])->name('checkout.pending');
        Route::get('/error', [App\Http\Controllers\Customer\CheckoutController::class, 'error'])->name('checkout.error');
    });

    // Order routes (authenticated users only)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Customer\OrderController::class, 'index'])->name('index');
        Route::get('/{orderNumber}', [App\Http\Controllers\Customer\OrderController::class, 'show'])->name('show');
        Route::post('/{orderNumber}/cancel', [App\Http\Controllers\Customer\OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{orderNumber}/refresh-payment', [App\Http\Controllers\Customer\OrderController::class, 'refreshPaymentStatus'])->name('refresh.payment');
        Route::get('/{orderNumber}/track', [App\Http\Controllers\Customer\OrderController::class, 'track'])->name('track');
        Route::post('/{orderNumber}/reorder', [App\Http\Controllers\Customer\OrderController::class, 'reorder'])->name('reorder');
        Route::get('/{orderNumber}/invoice', [App\Http\Controllers\Customer\OrderController::class, 'invoice'])->name('invoice');
        Route::post('/{orderNumber}/mark-delivered', [App\Http\Controllers\Customer\OrderController::class, 'markAsDelivered'])->name('mark-delivered');
    });

});

// Public API routes (no auth required)
Route::get('/api/settings/public', [App\Http\Controllers\Admin\SystemSettingsController::class, 'publicSettings'])->name('api.settings.public');

// Midtrans notification webhook (no auth required, but allow CSRF for frontend simulation)
Route::post('/payment/notification', [App\Http\Controllers\Customer\CheckoutController::class, 'notification'])
    ->name('payment.notification')
    ->withoutMiddleware(['auth']);

// Payment checking for demo (authenticated users only)
Route::middleware('auth')->group(function () {
    Route::post('/payment/check/{orderNumber}', [App\Http\Controllers\Customer\CheckoutController::class, 'checkPaymentStatus'])->name('payment.check');
});

// Location API routes (public - for system settings and non-authenticated access)
Route::get('/api/provinces/public', [App\Http\Controllers\Customer\AddressController::class, 'getProvinces'])->name('api.provinces.public');
Route::get('/api/cities/public', [App\Http\Controllers\Customer\AddressController::class, 'getCities'])->name('api.cities.public');
Route::get('/api/districts/public', [App\Http\Controllers\Customer\AddressController::class, 'getDistricts'])->name('api.districts.public');
Route::get('/api/villages/public', [App\Http\Controllers\Customer\AddressController::class, 'getVillages'])->name('api.villages.public');

// Owner routes (owner only)
Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('owner.dashboard');

    // Analytics and Reports
    Route::get('/analytics', [OwnerDashboardController::class, 'analytics'])->name('owner.analytics');
    Route::get('/export-report', [OwnerDashboardController::class, 'exportReport'])->name('owner.export-report');
    Route::get('/financial-reports', [OwnerDashboardController::class, 'financialReports'])->name('owner.financial-reports');
    Route::get('/business-reports', [OwnerDashboardController::class, 'businessReports'])->name('owner.business-reports');

    // Owner can access system settings (but not all admin features)
    Route::get('/settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('owner.settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('owner.settings.update');
    Route::post('/settings/sync', [App\Http\Controllers\Admin\SystemSettingsController::class, 'syncAPIs'])->name('owner.settings.sync');
});

// Admin routes (admin only)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

    // System settings routes
    Route::get('/settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'index'])->name('admin.settings.index');
    Route::put('/settings', [App\Http\Controllers\Admin\SystemSettingsController::class, 'update'])->name('admin.settings.update');
    Route::post('/settings/sync', [App\Http\Controllers\Admin\SystemSettingsController::class, 'syncAPIs'])->name('admin.settings.sync');

    // Category management routes
    Route::prefix('categories')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('admin.categories.index');
        Route::get('/data', [App\Http\Controllers\Admin\CategoryController::class, 'data'])->name('admin.categories.data');
        Route::get('/parents/list', [App\Http\Controllers\Admin\CategoryController::class, 'parents'])->name('admin.categories.parents');
        Route::post('/', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('admin.categories.show');
        Route::put('/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/{category}', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    });

    // Product management routes
    Route::prefix('products')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('admin.products.index');
        Route::get('/data', [App\Http\Controllers\Admin\ProductController::class, 'data'])->name('admin.products.data');
        Route::get('/categories/list', [App\Http\Controllers\Admin\ProductController::class, 'categories'])->name('admin.products.categories');
        Route::get('/stats', [App\Http\Controllers\Admin\ProductController::class, 'stats'])->name('admin.products.stats');
        Route::post('/bulk-update-stock', [App\Http\Controllers\Admin\ProductController::class, 'bulkUpdateStock'])->name('admin.products.bulk-update-stock');
        Route::post('/', [App\Http\Controllers\Admin\ProductController::class, 'store'])->name('admin.products.store');
        Route::get('/{product}', [App\Http\Controllers\Admin\ProductController::class, 'show'])->name('admin.products.show');
        Route::put('/{product}', [App\Http\Controllers\Admin\ProductController::class, 'update'])->name('admin.products.update');
        Route::delete('/{product}', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('admin.products.destroy');

        // Product image management routes
        Route::post('/{product}/images', [App\Http\Controllers\Admin\ProductController::class, 'uploadImages'])->name('admin.products.images.upload');
        Route::get('/{product}/images', [App\Http\Controllers\Admin\ProductController::class, 'getImages'])->name('admin.products.images.index');
        Route::put('/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'updateImage'])->name('admin.products.images.update');
        Route::delete('/{product}/images/{image}', [App\Http\Controllers\Admin\ProductController::class, 'deleteImage'])->name('admin.products.images.destroy');
        Route::post('/{product}/images/{image}/set-primary', [App\Http\Controllers\Admin\ProductController::class, 'setPrimaryImage'])->name('admin.products.images.set-primary');
        Route::post('/{product}/images/reorder', [App\Http\Controllers\Admin\ProductController::class, 'reorderImages'])->name('admin.products.images.reorder');
    });

    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/data', [App\Http\Controllers\Admin\UserController::class, 'data'])->name('admin.users.data');
        Route::post('/', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{user}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::put('/{user}', [App\Http\Controllers\Admin\UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{user}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Banner management routes
    Route::prefix('banners')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\BannerController::class, 'index'])->name('admin.banners.index');
        Route::get('/data', [App\Http\Controllers\Admin\BannerController::class, 'data'])->name('admin.banners.data');
        Route::post('/', [App\Http\Controllers\Admin\BannerController::class, 'store'])->name('admin.banners.store');
        Route::get('/{banner}', [App\Http\Controllers\Admin\BannerController::class, 'show'])->name('admin.banners.show');
        Route::put('/{banner}', [App\Http\Controllers\Admin\BannerController::class, 'update'])->name('admin.banners.update');
        Route::delete('/{banner}', [App\Http\Controllers\Admin\BannerController::class, 'destroy'])->name('admin.banners.destroy');
    });

    // Order management routes
    Route::prefix('orders')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/data', [App\Http\Controllers\Admin\OrderController::class, 'data'])->name('admin.orders.data');
        Route::get('/statistics', [App\Http\Controllers\Admin\OrderController::class, 'statistics'])->name('admin.orders.statistics');
        Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('admin.orders.update-status');
        Route::post('/bulk-update-status', [App\Http\Controllers\Admin\OrderController::class, 'bulkUpdateStatus'])->name('admin.orders.bulk-update-status');
    });


});

// Customer routes
Route::prefix('customer')->middleware(['auth', 'role:customer,admin,owner'])->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
});

// Homepage routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/product/{id}', [HomeController::class, 'productDetail'])->name('product.detail');
