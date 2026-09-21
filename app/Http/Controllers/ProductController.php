<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display products with search, price filter, sorting, pagination,
     * and dynamic permission-based UI rendering.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = [
            'id',
            'name',
            'price',
            'cost_price',
            'profit_margin',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $products = Product::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($minPrice !== null && $minPrice !== '', function ($query) use ($minPrice) {
                $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice !== null && $maxPrice !== '', function ($query) use ($maxPrice) {
                $query->where('price', '<=', $maxPrice);
            })
            ->orderBy($sort, $direction)
            ->paginate(5)
            ->withQueryString();

        $statistics = [
            'total' => Product::count(),
            'average_price' => Product::avg('price'),
            'minimum_price' => Product::min('price'),
            'maximum_price' => Product::max('price'),
            'total_valuation' => Product::sum('price'),
        ];

        return view(
            'products.index',
            compact(
                'products',
                'search',
                'minPrice',
                'maxPrice',
                'sort',
                'direction',
                'statistics'
            )
        );
    }

    /**
     * Display product statistics.
     */
    public function statistics(): View
    {
        $statistics = [
            'total' => Product::count(),
            'average_price' => Product::avg('price'),
            'minimum_price' => Product::min('price'),
            'maximum_price' => Product::max('price'),
        ];

        $latestProducts = Product::latest()->limit(10)->get();
        $mostExpensive = Product::orderByDesc('price')->limit(5)->get();
        $cheapest = Product::orderBy('price')->limit(5)->get();

        return view(
            'products.statistics',
            compact(
                'statistics',
                'latestProducts',
                'mostExpensive',
                'cheapest'
            )
        );
    }

    /**
     * Export products as CSV respecting field-level masking.
     */
    public function export(Request $request): StreamedResponse
    {
        $canViewCosts = auth()->check() && auth()->user()->can('view product costs');
        $products = Product::orderBy('id')->get();

        $fileName = 'products_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($products, $canViewCosts) {
            $file = fopen('php://output', 'w');

            $csvHeader = ['ID', 'Product Name', 'Selling Price (₹)'];
            if ($canViewCosts) {
                $csvHeader[] = 'Cost Price (₹)';
                $csvHeader[] = 'Profit Margin (%)';
                $csvHeader[] = 'Supplier Code';
            }
            $csvHeader[] = 'Created At';

            fputcsv($file, $csvHeader);

            foreach ($products as $product) {
                $row = [
                    $product->id,
                    $product->name,
                    $product->price,
                ];

                if ($canViewCosts) {
                    $row[] = $product->cost_price ?? 'N/A';
                    $row[] = ($product->profit_margin ?? 0) . '%';
                    $row[] = $product->supplier_code ?? 'N/A';
                }

                $row[] = $product->created_at?->format('Y-m-d H:i:s');
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk delete selected products.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $count = Product::whereIn('id', $validated['product_ids'])->delete();

        return redirect()
            ->route('products.index')
            ->with('success', $count . ' product(s) deleted successfully.');
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('products.create');
    }

    /**
     * Store product with field-level permission checks.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];

        $canEditCosts = auth()->check() && auth()->user()->can('edit product costs');
        if ($canEditCosts) {
            $rules['cost_price'] = ['nullable', 'numeric', 'min:0'];
            $rules['supplier_code'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        // Auto-compute profit margin if cost price is provided
        if (isset($validated['cost_price']) && $validated['price'] > 0) {
            $profit = $validated['price'] - $validated['cost_price'];
            $validated['profit_margin'] = round(($profit / $validated['price']) * 100, 2);
        }

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
     * Update product with field-level permission checks.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];

        $canEditCosts = auth()->check() && auth()->user()->can('edit product costs');
        if ($canEditCosts) {
            $rules['cost_price'] = ['nullable', 'numeric', 'min:0'];
            $rules['supplier_code'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        if (isset($validated['cost_price']) && $validated['price'] > 0) {
            $profit = $validated['price'] - $validated['cost_price'];
            $validated['profit_margin'] = round(($profit / $validated['price']) * 100, 2);
        }

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