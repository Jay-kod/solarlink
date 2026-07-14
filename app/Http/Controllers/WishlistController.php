<?php

namespace App\Http\Controllers;

use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WishlistController extends Controller
{
    /**
     * Display the user's wishlist items.
     */
    public function index()
    {
        $user = Auth::user();
        $wishlistItems = $user->wishlistItems()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'wishlist_item_id' => $item->id,
                    'name' => $item->product->name,
                    'price' => (float)$item->product->price,
                    'originalPrice' => $item->product->original_price ? (float)$item->product->original_price : null,
                    'rating' => (float)$item->product->rating,
                    'reviewsCount' => $item->product->reviews_count,
                    'image' => $item->product->image,
                    'category' => $item->product->category,
                    'stock' => $item->product->stock,
                    'description' => $item->product->description,
                    'specs' => $item->product->specs,
                ];
            });

        return Inertia::render('Customer/Marketplace/Wishlist', [
            'wishlistItems' => $wishlistItems,
        ]);
    }

    /**
     * Toggle a product in the wishlist (add if not present, remove if present).
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $wishlistItem = $user->wishlistItems()->where('product_id', $productId)->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
        } else {
            $user->wishlistItems()->create([
                'product_id' => $productId,
            ]);
        }

        return redirect()->back();
    }
}
