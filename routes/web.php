<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SolarApplianceController;
use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\Product;
use App\Models\Booking;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public Marketing Pages
Route::get('/', function () {
    return Inertia::render('Public/Landing');
});

Route::get('/features', function () {
    return Inertia::render('Public/Features');
});

Route::get('/pricing', function () {
    return Inertia::render('Public/Pricing', [
        'pricingPlans' => PricingPlan::all()
    ]);
});

Route::get('/about', function () {
    return Inertia::render('Public/About');
});

Route::get('/faq', function () {
    return Inertia::render('Public/FAQ', [
        'faqs' => Faq::orderBy('order')->get()
    ]);
});

Route::get('/contact', function () {
    return Inertia::render('Public/Contact');
});

Route::get('/404', function () {
    return Inertia::render('Public/NotFound');
});

// Protected User (Customer) Routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/user', function () {
        return Inertia::render('Customer/Dashboard');
    });

    Route::get('/user/map', function () {
        return Inertia::render('Customer/Map');
    });

    // Bookings routes
    Route::get('/user/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/user/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::post('/user/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/user/bookings/{booking}/pay', [BookingController::class, 'pay'])->name('bookings.pay');

    // Solar Appliances routes
    Route::get('/user/appliances', [SolarApplianceController::class, 'index'])->name('appliances.index');
    Route::post('/user/appliances', [SolarApplianceController::class, 'store'])->name('appliances.store');
    Route::delete('/user/appliances/{appliance}', [SolarApplianceController::class, 'destroy'])->name('appliances.destroy');

    // Payments route
    Route::get('/user/payments', function () {
        $user = auth()->user();
        if (!$user) return redirect()->back();

        $orders = \App\Models\Order::where('user_id', $user->id)
            ->with('product')
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'reference' => 'INV-ORD-' . $order->id,
                    'type' => 'Procurement',
                    'item' => $order->product ? $order->product->name : 'Solar Product',
                    'quantity' => $order->quantity,
                    'amount' => (float)$order->total_price,
                    'status' => $order->status === 'delivered' ? 'paid' : ($order->status === 'cancelled' ? 'refunded' : 'processing'),
                    'date' => $order->order_date->format('Y-m-d'),
                ];
            });

        $bookings = \App\Models\Booking::where('user_id', $user->id)
            ->with('technicianProfile.user')
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'reference' => 'INV-SRV-' . $booking->id,
                    'type' => 'Maintenance',
                    'item' => $booking->service_type . ' (Tech: ' . ($booking->technicianProfile ? $booking->technicianProfile->user->name : 'N/A') . ')',
                    'quantity' => 1,
                    'amount' => (float)$booking->cost,
                    'status' => $booking->payment_status === 'paid' ? 'paid' : ($booking->status === 'cancelled' ? 'cancelled' : 'unpaid'),
                    'date' => $booking->date->format('Y-m-d'),
                ];
            });

        $payments = $orders->concat($bookings)->sortByDesc('date')->values()->toArray();

        return Inertia::render('Customer/Payments', [
            'payments' => $payments,
        ]);
    })->name('payments.index');

    Route::get('/user/marketplace', function () {
        $user = auth()->user();
        $products = Product::all()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'price' => (float)$product->price,
                'originalPrice' => $product->original_price ? (float)$product->original_price : null,
                'rating' => (float)$product->rating,
                'reviewsCount' => $product->reviews_count,
                'image' => $product->image,
                'description' => $product->description,
                'specs' => $product->specs,
                'stock' => $product->stock,
                'features' => $product->features,
                'isFeatured' => (bool)$product->is_featured,
            ];
        });

        $wishlistIds = $user ? $user->wishlistItems()->pluck('product_id')->toArray() : [];
        
        $cartItems = $user ? $user->cartItems()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => (float)$item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image,
                    'category' => $item->product->category,
                    'stock' => $item->product->stock,
                ];
            })->toArray() : [];

        return Inertia::render('Customer/Marketplace/Index', [
            'products' => $products,
            'wishlistIds' => $wishlistIds,
            'cartItems' => $cartItems,
        ]);
    });

    // Cart Routes
    Route::get('/user/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/user/cart', [App\Http\Controllers\CartController::class, 'store'])->name('cart.store');
    Route::patch('/user/cart/{product}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/user/cart/{product}', [App\Http\Controllers\CartController::class, 'destroy'])->name('cart.destroy');

    // Wishlist Routes
    Route::get('/user/wishlist', [App\Http\Controllers\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/user/wishlist', [App\Http\Controllers\WishlistController::class, 'store'])->name('wishlist.store');

    // Checkout Routes
    Route::get('/user/checkout', function () {
        $user = auth()->user();
        $cartItems = $user ? $user->cartItems()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => (float)$item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image,
                    'category' => $item->product->category,
                    'stock' => $item->product->stock,
                ];
            })->toArray() : [];

        return Inertia::render('Customer/Marketplace/Checkout', [
            'cartItems' => $cartItems,
            'status' => session('status'),
        ]);
    });

    Route::post('/user/checkout', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'shipping_address' => ['required', 'string'],
            'card_number' => ['required', 'string'],
        ]);

        $user = auth()->user();
        if (!$user) return redirect()->back();

        $cartItems = $user->cartItems()->with('product')->get();
        if ($cartItems->isEmpty()) {
            return redirect()->back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Generate orders
        foreach ($cartItems as $item) {
            \App\Models\Order::create([
                'user_id' => $user->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'total_price' => $item->product->price * $item->quantity,
                'status' => 'processing',
                'order_date' => now(),
                'shipping_address' => $request->shipping_address,
                'tracking_number' => 'SL-' . strtoupper(bin2hex(random_bytes(4))),
            ]);

            // Reduce stock
            $item->product->decrement('stock', $item->quantity);
        }

        // Clear Cart
        $user->cartItems()->delete();

        return redirect()->back()->with('status', 'order-placed');
    });

    Route::get('/user/analytics', function () {
        return Inertia::render('Customer/Analytics');
    });

    Route::get('/user/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');

    Route::get('/user/settings', function () {
        return Inertia::render('Customer/Settings');
    });
});

// Protected Technician Routes
Route::middleware(['auth', 'role:technician'])->group(function () {
    Route::get('/technician', function () {
        return Inertia::render('Technician/Dashboard');
    });

    Route::get('/technician/jobs', function () {
        return Inertia::render('Technician/Jobs/Active');
    });

    Route::get('/technician/calendar', function () {
        return Inertia::render('Technician/Calendar');
    });

    Route::get('/technician/earnings', function () {
        return Inertia::render('Technician/Earnings');
    });

    Route::get('/technician/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('technician.chat.index');
});

// Protected Vendor Routes
Route::middleware(['auth', 'role:vendor'])->group(function () {
    Route::get('/vendor', function () {
        return Inertia::render('Vendor/Dashboard');
    });

    Route::get('/vendor/products', function () {
        return Inertia::render('Vendor/Products/Index');
    });

    Route::get('/vendor/orders', function () {
        return Inertia::render('Vendor/Orders/Index');
    });

    Route::get('/vendor/store', function () {
        return Inertia::render('Vendor/Store');
    });

    Route::get('/vendor/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('vendor.chat.index');
});

// Protected Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('Admin/Dashboard');
    });

    Route::get('/admin/users', function () {
        return Inertia::render('Admin/Users/Index');
    });

    Route::get('/admin/technicians', function () {
        return Inertia::render('Admin/Technicians/Index');
    });

    Route::get('/admin/vendors', function () {
        return Inertia::render('Admin/Vendors/Index');
    });

    Route::get('/admin/products', function () {
        return Inertia::render('Admin/Products/Index');
    });

    Route::get('/admin/blog', function () {
        return Inertia::render('Admin/Blog/Index');
    });

    Route::get('/admin/settings', function () {
        return Inertia::render('Admin/Settings');
    });

    Route::get('/admin/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('admin.chat.index');
});

// Profile routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chat API routes
    Route::get('/api/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/api/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'storeMessage']);
    Route::post('/api/conversations/{conversation}/conclude', [App\Http\Controllers\ChatController::class, 'conclude']);
    Route::delete('/api/conversations/{conversation}', [App\Http\Controllers\ChatController::class, 'destroy']);
});

// Load Breeze Authentication Routes
require __DIR__.'/auth.php';
