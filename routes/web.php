<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\SolarApplianceController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\TechnicianMaintenanceController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\VendorProcurementController;
use App\Http\Controllers\VendorProductController;
use App\Http\Controllers\ServiceNotificationController;
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
})->name('home');

// Session-isolated dashboard previews for side-by-side testing
Route::prefix('preview')->group(function () {
    Route::get('/customer', function () {
        return Inertia::render('Customer/Dashboard');
    })->name('preview.customer');

    Route::get('/technician', function () {
        return Inertia::render('Technician/Dashboard');
    })->name('preview.technician');

    Route::get('/vendor', function () {
        return Inertia::render('Vendor/Dashboard');
    })->name('preview.vendor');

    Route::get('/admin', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('preview.admin');
});

Route::get('/features', function () {
    return Inertia::render('Public/Features');
});

Route::get('/pricing', function () {
    return Inertia::render('Public/Pricing', [
        'pricingPlans' => Cache::remember('pricing_plans', 3600, function () {
            return PricingPlan::all();
        })
    ]);
});

Route::get('/about', function () {
    return Inertia::render('Public/About');
});

Route::get('/faq', function () {
    return Inertia::render('Public/FAQ', [
        'faqs' => Cache::remember('faqs_ordered', 3600, function () {
            return Faq::orderBy('order')->get();
        })
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
    })->name('user.dashboard');

    Route::get('/user/map', [MapController::class, 'index'])->name('map.index');

    // Maintenance request routes
    Route::get('/user/maintenance', [MaintenanceRequestController::class, 'index'])->name('maintenance.index');
    Route::get('/user/maintenance/create', [MaintenanceRequestController::class, 'create'])->name('maintenance.create');
    Route::get('/user/maintenance/{maintenanceRequest}', [MaintenanceRequestController::class, 'show'])->name('maintenance.show');
    Route::post('/user/maintenance', [MaintenanceRequestController::class, 'store'])->name('maintenance.store');
    Route::post('/user/maintenance/{maintenanceRequest}/cancel', [MaintenanceRequestController::class, 'cancel'])->name('maintenance.cancel');
    Route::post('/user/maintenance/{maintenanceRequest}/pay', [MaintenanceRequestController::class, 'pay'])->name('maintenance.pay');

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

    // Procurement (RFQ) routes
    Route::get('/user/procurement', [ProcurementController::class, 'index'])->name('procurement.index');
    Route::post('/user/procurement', [ProcurementController::class, 'store'])->name('procurement.store');
    Route::post('/user/procurement/quotes/{quote}/approve', [ProcurementController::class, 'approveQuote'])->name('procurement.quote.approve');

    Route::get('/user/settings', function () {
        return Inertia::render('Customer/Settings');
    });
});

// Protected Technician Routes
Route::middleware(['auth', 'role:technician'])->group(function () {
    Route::get('/technician', function () {
        return Inertia::render('Technician/Dashboard');
    })->name('technician.dashboard');

    Route::get('/technician/jobs', [TechnicianMaintenanceController::class, 'index'])->name('technician.jobs.index');
    Route::get('/technician/jobs/{maintenanceRequest}', [TechnicianMaintenanceController::class, 'show'])->name('technician.jobs.show');
    Route::post('/technician/jobs/{maintenanceRequest}/accept', [TechnicianMaintenanceController::class, 'accept'])->name('technician.jobs.accept');
    Route::post('/technician/jobs/{maintenanceRequest}/reject', [TechnicianMaintenanceController::class, 'reject'])->name('technician.jobs.reject');
    Route::post('/technician/jobs/{maintenanceRequest}/start', [TechnicianMaintenanceController::class, 'start'])->name('technician.jobs.start');
    Route::post('/technician/jobs/{maintenanceRequest}/complete', [TechnicianMaintenanceController::class, 'complete'])->name('technician.jobs.complete');
    Route::get('/technician/requests', [TechnicianMaintenanceController::class, 'index'])->name('technician.requests.index');
    Route::post('/technician/requests/{maintenanceRequest}/status', [TechnicianMaintenanceController::class, 'updateStatus'])->name('technician.requests.status');

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
    })->name('vendor.dashboard');

    Route::get('/vendor/products', [VendorProductController::class, 'index'])->name('vendor.products.index');
    Route::get('/vendor/products/create', [VendorProductController::class, 'create'])->name('vendor.products.create');
    Route::post('/vendor/products', [VendorProductController::class, 'store'])->name('vendor.products.store');
    Route::get('/vendor/products/{product}', [VendorProductController::class, 'show'])->name('vendor.products.show');
    Route::put('/vendor/products/{product}', [VendorProductController::class, 'update'])->name('vendor.products.update');
    Route::delete('/vendor/products/{product}', [VendorProductController::class, 'destroy'])->name('vendor.products.destroy');

    Route::get('/vendor/orders', function () {
        return Inertia::render('Vendor/Orders/Index');
    });

    Route::get('/vendor/store', function () {
        return Inertia::render('Vendor/Store');
    });

    Route::get('/vendor/rfqs', [VendorProcurementController::class, 'index'])->name('vendor.rfqs.index');
    Route::post('/vendor/rfqs/{procurementRequest}/quote', [VendorProcurementController::class, 'quote'])->name('vendor.rfqs.quote');

    Route::get('/vendor/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('vendor.chat.index');
});

// Protected Admin Routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/users', function () {
        $users = \App\Models\User::select('id', 'name', 'email', 'role', 'avatar', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'customer',
                    'status' => 'active',
                    'registered' => $user->created_at->format('M d, Y'),
                    'avatar' => $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0ea5e9&color=fff',
                ];
            });

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    });

    Route::delete('/admin/users/{user}', function (\App\Models\User $user) {
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    });

    Route::get('/admin/technicians', function () {
        $technicians = \App\Models\User::where('role', 'technician')
            ->with('technicianProfile')
            ->get()
            ->map(function ($tech) {
                $profile = $tech->technicianProfile;
                return [
                    'id' => $tech->id,
                    'name' => $tech->name,
                    'avatar' => $tech->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($tech->name) . '&background=0ea5e9&color=fff',
                    'license' => $profile->cert_name ?? 'Pending Certification',
                    'experience' => ($profile->experience ?? 0) . ' Years',
                    'specialization' => $profile && is_array($profile->skills) ? implode(', ', $profile->skills) : 'General Technician',
                    'rating' => $profile->rating ?? 0.0,
                    'completedJobs' => $profile->review_count ?? 0,
                    'status' => $profile->approval_status ?? 'pending',
                ];
            });

        return Inertia::render('Admin/Technicians/Index', [
            'technicians' => $technicians
        ]);
    });

    Route::patch('/admin/technicians/{user}/status', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
        $request->validate(['status' => 'required|in:verified,pending,suspended']);
        if ($user->technicianProfile) {
            $user->technicianProfile->update(['approval_status' => $request->status]);
        } else {
            $user->technicianProfile()->create(['approval_status' => $request->status]);
        }
        return back()->with('success', 'Technician status updated.');
    });

    Route::get('/admin/vendors', function () {
        $vendors = \App\Models\User::where('role', 'vendor')
            ->with('vendorProfile')
            ->get()
            ->map(function ($vendor) {
                $profile = $vendor->vendorProfile;
                return [
                    'id' => $vendor->id,
                    'name' => $profile->store_name ?? $vendor->name,
                    'vat' => $profile->vat_number ?? 'N/A',
                    'headquarters' => $profile->company_address ?? 'N/A',
                    'email' => $vendor->email,
                    'joined' => $vendor->created_at ? $vendor->created_at->format('M d, Y') : 'Unknown',
                    'status' => $profile->approval_status ?? 'pending',
                ];
            });

        return Inertia::render('Admin/Vendors/Index', [
            'vendors' => $vendors
        ]);
    });

    Route::patch('/admin/vendors/{user}/status', function (\Illuminate\Http\Request $request, \App\Models\User $user) {
        $request->validate(['status' => 'required|in:verified,pending,suspended']);
        if ($user->vendorProfile) {
            $user->vendorProfile->update(['approval_status' => $request->status]);
        } else {
            $user->vendorProfile()->create(['approval_status' => $request->status]);
        }
        return back()->with('success', 'Vendor status updated.');
    });

    Route::get('/admin/products', function () {
        $products = \App\Models\Product::with('supplier.user')->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'rating' => $product->rating ?? 0.0,
                'reviewsCount' => $product->reviews_count ?? 0,
                'image' => $product->image ?? 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=200&h=200&fit=crop',
                'description' => $product->description ?? '',
                'specs' => $product->specs ?? (object)[],
                'stock' => $product->stock ?? 0,
                'features' => $product->features ?? [],
                'vendorName' => $product->supplier ? $product->supplier->user->name : 'Unknown',
                'submittedDate' => $product->created_at ? $product->created_at->format('M d, Y') : 'Unknown',
                'approvalStatus' => $product->approval_status ?? 'pending',
            ];
        });

        return Inertia::render('Admin/Products/Index', [
            'products' => $products
        ]);
    });

    Route::patch('/admin/products/{product}/status', function (\Illuminate\Http\Request $request, \App\Models\Product $product) {
        $request->validate(['status' => 'required|in:approved,pending,rejected']);
        $product->update(['approval_status' => $request->status]);
        return back()->with('success', 'Product status updated.');
    });

    Route::get('/admin/maintenance', function () {
        $requests = \App\Models\MaintenanceRequest::with(['user', 'appliance', 'technicianProfile.user'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Maintenance/Index', [
            'requests' => $requests->map(fn ($request) => [
                'id' => $request->id,
                'customer' => $request->user?->name,
                'faultType' => $request->fault_type ?? $request->issue_type ?? 'other',
                'status' => $request->status,
                'location' => $request->location ?? $request->location_address,
                'technician' => $request->technicianProfile?->user?->name,
                'appliance' => $request->appliance?->name,
            ]),
        ]);
    })->name('admin.maintenance.index');

    Route::post('/admin/maintenance/{maintenanceRequest}/assign', [MaintenanceRequestController::class, 'assignTechnician'])->name('admin.maintenance.assign');

    Route::get('/admin/blog', function () {
        $posts = \App\Models\BlogPost::orderBy('created_at', 'desc')->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'author' => $post->author,
                'category' => $post->category,
                'date' => $post->created_at->format('M d, Y'),
                'views' => $post->views >= 1000 ? round($post->views / 1000, 1) . 'k' : (string)$post->views,
                'status' => $post->status,
                'content' => $post->content,
            ];
        });
        return Inertia::render('Admin/Blog/Index', [
            'blogPosts' => $posts
        ]);
    });

    Route::post('/admin/blog', function (\Illuminate\Http\Request $request) {
        $data = $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'category' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|in:published,draft'
        ]);
        \App\Models\BlogPost::create($data);
        return back()->with('success', 'Blog post created.');
    });

    Route::patch('/admin/blog/{post}/status', function (\Illuminate\Http\Request $request, \App\Models\BlogPost $post) {
        $request->validate(['status' => 'required|in:published,draft']);
        $post->update(['status' => $request->status]);
        return back()->with('success', 'Blog post status updated.');
    });

    Route::delete('/admin/blog/{post}', function (\App\Models\BlogPost $post) {
        $post->delete();
        return back()->with('success', 'Blog post deleted.');
    });

    Route::get('/admin/settings', function () {
        return Inertia::render('Admin/Settings');
    });

    Route::get('/admin/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('admin.chat.index');
});

// Profile routes for all authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
    Route::get('/locations/data', [LocationController::class, 'data'])->name('locations.data');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    Route::patch('/locations/{savedLocation}', [LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{savedLocation}', [LocationController::class, 'destroy'])->name('locations.destroy');
    Route::put('/locations/live', [LocationController::class, 'updateLive'])->name('locations.live.update');
    Route::get('/admin/locations', [LocationController::class, 'index'])->middleware('role:admin')->name('admin.locations.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification actions
    Route::post('/notifications/{notification}/read', [ServiceNotificationController::class, 'markRead'])->name('notifications.read');

    // Chat API routes
    Route::get('/api/users/search', [App\Http\Controllers\ChatController::class, 'searchUsers']);
    Route::post('/api/conversations', [App\Http\Controllers\ChatController::class, 'store']);
    Route::get('/api/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'getMessages']);
    Route::post('/api/conversations/{conversation}/messages', [App\Http\Controllers\ChatController::class, 'storeMessage']);
    Route::post('/api/conversations/{conversation}/conclude', [App\Http\Controllers\ChatController::class, 'conclude']);
    Route::post('/api/conversations/{conversation}/members', [App\Http\Controllers\ChatController::class, 'addMembers']);
    Route::delete('/api/conversations/{conversation}/members/{user}', [App\Http\Controllers\ChatController::class, 'removeMember']);
    Route::delete('/api/conversations/{conversation}', [App\Http\Controllers\ChatController::class, 'destroy']);
});

// Load Breeze Authentication Routes
require __DIR__.'/auth.php';
