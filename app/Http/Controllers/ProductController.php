<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;


class ProductController extends Controller
{
    /**
     * Display products with search, price filter,
     * sorting and pagination.
     */
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));

        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        $sort = $request->input('sort', 'created_at');

        $direction = $request->input('direction', 'desc');

        /*
         * Only allow safe sortable columns.
         */
        $allowedSorts = [
            'id',
            'name',
            'price',
            'created_at',
        ];

        if (!in_array($sort, $allowedSorts, true)) {
            $sort = 'created_at';
        }

        /*
         * Only allow asc / desc.
         */
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        $products = Product::query()

            /*
             * Search by product name.
             */
            ->when($search, function ($query) use ($search) {
                $query->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
            })

            /*
             * Minimum price.
             */
            ->when(
                $minPrice !== null
                && $minPrice !== '',
                function ($query) use ($minPrice) {
                    $query->where(
                        'price',
                        '>=',
                        $minPrice
                    );
                }
            )

            /*
             * Maximum price.
             */
            ->when(
                $maxPrice !== null
                && $maxPrice !== '',
                function ($query) use ($maxPrice) {
                    $query->where(
                        'price',
                        '<=',
                        $maxPrice
                    );
                }
            )

            /*
             * Sorting.
             */
            ->orderBy($sort, $direction)

            /*
             * Numeric pagination.
             */
            ->paginate(5)

            /*
             * Preserve search/filter/sort
             * parameters while changing pages.
             */
            ->withQueryString();

        /*
         * Statistics for the current product database.
         */
        $statistics = [
            'total' => Product::count(),

            'average_price' => Product::avg('price'),

            'minimum_price' => Product::min('price'),

            'maximum_price' => Product::max('price'),
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

        $latestProducts = Product::latest()
            ->limit(10)
            ->get();

        $mostExpensive = Product::orderByDesc('price')
            ->limit(5)
            ->get();

        $cheapest = Product::orderBy('price')
            ->limit(5)
            ->get();

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
     * Export products as CSV.
     */
   public function export(Request $request): StreamedResponse
    {
        $products = Product::orderBy('id')->get();

        $fileName = 'products_' .
            now()->format('Y_m_d_H_i_s') .
            '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' =>
                'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            /*
             * CSV header.
             */
            fputcsv($file, [
                'ID',
                'Name',
                'Price',
                'Created At',
            ]);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->price,
                    $product->created_at?->format(
                        'Y-m-d H:i:s'
                    ),
                ]);
            }

            fclose($file);
        };

        return response()->stream(
            $callback,
            200,
            $headers
        );
    }

    /**
     * Bulk delete selected products.
     */
    public function bulkDelete(
        Request $request
    ): RedirectResponse {
        $validated = $request->validate([
            'product_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'product_ids.*' => [
                'integer',
                'exists:products,id',
            ],
        ]);

        $count = Product::whereIn(
            'id',
            $validated['product_ids']
        )->delete();

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                $count . ' product(s) deleted successfully.'
            );
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
    public function store(
        Request $request
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

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product created successfully.'
            );
    }

    /**
     * Show edit form.
     */
    public function edit(
        Product $product
    ): View {
        return view(
            'products.edit',
            compact('product')
        );
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
            ->with(
                'success',
                'Product updated successfully.'
            );
    }

    /**
     * Delete product.
     */
    public function destroy(
        Product $product
    ): RedirectResponse {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'Product deleted successfully.'
            );
    }
}