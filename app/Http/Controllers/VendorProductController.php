<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VendorProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::where('vendor_id', $request->user()->id)
            ->latest()
            ->get();

        return Inertia::render('Vendor/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('Vendor/Products/Index', [
            'products' => Product::where('vendor_id', $request->user()->id)->latest()->get(),
        ]);
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->id || $request->user()->role === 'admin', 403);

        return Inertia::render('Vendor/Products/Index', [
            'products' => Product::where('vendor_id', $request->user()->id)->latest()->get(),
            'selectedProduct' => $product,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:panels,batteries,inverters,accessories'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'specs' => ['nullable', 'array'],
            'features' => ['nullable', 'array'],
        ]);

        $product = Product::create([
            'vendor_id' => $request->user()->id,
            'name' => $validated['name'],
            'category' => $validated['category'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? '',
            'image' => $validated['image'] ?? 'https://images.unsplash.com/photo-1509391366360-2e959784a276',
            'specs' => $validated['specs'] ?? [],
            'features' => $validated['features'] ?? [],
            'is_featured' => false,
            'is_verified' => false,
            'approval_status' => 'pending',
        ]);

        return redirect()->route('vendor.products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->id || $request->user()->role === 'admin', 403);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'category' => ['sometimes', 'in:panels,batteries,inverters,accessories'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'string'],
            'specs' => ['sometimes', 'nullable', 'array'],
            'features' => ['sometimes', 'nullable', 'array'],
        ]);

        $product->fill($validated)->save();

        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->vendor_id === $request->user()->id || $request->user()->role === 'admin', 403);
        $product->delete();

        return redirect()->route('vendor.products.index')->with('success', 'Product deleted successfully.');
    }
}
