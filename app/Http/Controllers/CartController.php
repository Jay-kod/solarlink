<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the user's cart items.
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()
            ->with('product')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->product->id,
                    'cart_item_id' => $item->id,
                    'name' => $item->product->name,
                    'price' => (float)$item->product->price,
                    'quantity' => $item->quantity,
                    'image' => $item->product->image,
                    'category' => $item->product->category,
                    'stock' => $item->product->stock,
                ];
            });

        return Inertia::render('Customer/Marketplace/Cart', [
            'cartItems' => $cartItems,
        ]);
    }

    /**
     * Add an item to the cart or increment its quantity.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['integer', 'min:1'],
        ]);

        $user = Auth::user();
        $productId = $request->product_id;
        $qty = $request->input('quantity', 1);

        $product = Product::findOrFail($productId);

        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $qty;
            if ($newQuantity > $product->stock) {
                $newQuantity = $product->stock;
            }
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            if ($qty > $product->stock) {
                $qty = $product->stock;
            }
            if ($qty > 0) {
                $user->cartItems()->create([
                    'product_id' => $productId,
                    'quantity' => $qty,
                ]);
            }
        }

        return redirect()->back();
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, $productId)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($productId);

        $cartItem = $user->cartItems()->where('product_id', $productId)->first();

        if ($cartItem) {
            $qty = $request->quantity;
            if ($qty > $product->stock) {
                $qty = $product->stock;
            }
            $cartItem->update(['quantity' => $qty]);
        }

        return redirect()->back();
    }

    /**
     * Remove an item from the cart.
     */
    public function destroy($productId)
    {
        $user = Auth::user();
        $user->cartItems()->where('product_id', $productId)->delete();

        return redirect()->back();
    }
}
