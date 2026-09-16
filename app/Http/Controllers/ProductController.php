<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display products.
     */
    public function index(): View
    {
        $products = Product::latest()->get();

        return view('products.index', compact('products'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Store product.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update product.
     */
    public function update(
        Request $request,
        Product $product
    ): RedirectResponse {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}