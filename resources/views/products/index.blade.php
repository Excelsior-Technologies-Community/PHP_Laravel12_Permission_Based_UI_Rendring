<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        📦 Products Catalog
                    </h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 border border-indigo-200">
                        Permission-Gated UI
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-1">
                    Manage catalog items with dynamic column masking and role-based action locks.
                </p>
            </div>

            <!-- Header Action Buttons (Wrapped in Permission Gates) -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Statistics Button -->
                <x-permission-gate permission="view products" tooltip="Requires 'view products' permission">
                    <a href="{{ route('products.statistics') }}" class="inline-flex items-center gap-1.5 bg-purple-600 hover:bg-purple-700 text-white px-3.5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        <span>📊</span>
                        <span>Statistics</span>
                    </a>
                </x-permission-gate>

                <!-- Export CSV Button -->
                <x-permission-gate permission="export products" tooltip="Requires 'export products' permission">
                    <a href="{{ route('products.export') }}" class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white px-3.5 py-2 rounded-lg text-sm font-medium transition shadow-sm">
                        <span>📥</span>
                        <span>Export CSV</span>
                    </a>
                </x-permission-gate>

                <!-- Add Product Button -->
                <x-permission-gate permission="create products" tooltip="Requires 'create products' permission">
                    <a href="{{ route('products.create') }}" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm">
                        <span>+</span>
                        <span>Add Product</span>
                    </a>
                </x-permission-gate>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- Statistics Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Products</p>
                    <p class="text-2xl font-black text-gray-800 mt-1">{{ $statistics['total'] }}</p>
                </div>
                <span class="text-3xl p-2 rounded-xl bg-blue-50 text-blue-600">📦</span>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Avg Selling Price</p>
                    <p class="text-2xl font-black text-emerald-600 mt-1">₹{{ number_format($statistics['average_price'] ?? 0, 2) }}</p>
                </div>
                <span class="text-3xl p-2 rounded-xl bg-emerald-50 text-emerald-600">🏷️</span>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Min Price</p>
                    <p class="text-2xl font-black text-sky-600 mt-1">₹{{ number_format($statistics['minimum_price'] ?? 0, 2) }}</p>
                </div>
                <span class="text-3xl p-2 rounded-xl bg-sky-50 text-sky-600">📉</span>
            </div>

            <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400">Max Price</p>
                    <p class="text-2xl font-black text-purple-600 mt-1">₹{{ number_format($statistics['maximum_price'] ?? 0, 2) }}</p>
                </div>
                <span class="text-3xl p-2 rounded-xl bg-purple-50 text-purple-600">📈</span>
            </div>
        </div>

        {{-- Search / Filters Box --}}
        <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-5">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Search Product</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name..." class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Min Price</label>
                    <input type="number" name="min_price" value="{{ $minPrice }}" min="0" step="0.01" placeholder="0" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Max Price</label>
                    <input type="number" name="max_price" value="{{ $maxPrice }}" min="0" step="0.01" placeholder="99999" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Sort By</label>
                    <select name="sort" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="created_at" @selected($sort === 'created_at')>Latest Added</option>
                        <option value="name" @selected($sort === 'name')>Product Name</option>
                        <option value="price" @selected($sort === 'price')>Selling Price</option>
                        <option value="cost_price" @selected($sort === 'cost_price')>Cost Price</option>
                        <option value="profit_margin" @selected($sort === 'profit_margin')>Profit Margin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Direction</label>
                    <select name="direction" class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="desc" @selected($direction === 'desc')>Descending ↓</option>
                        <option value="asc" @selected($direction === 'asc')>Ascending ↑</option>
                    </select>
                </div>

                <div class="lg:col-span-6 flex gap-2 pt-2 border-t border-gray-100">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                        Apply Filters
                    </button>
                    <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-1.5 rounded-lg text-xs font-semibold transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Bulk Actions Bar & Table --}}
        <form method="POST" action="{{ route('products.bulk-delete') }}" id="bulkDeleteForm">
            @csrf
            @method('DELETE')

            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-500 font-mono">
                    Showing {{ $products->count() }} of {{ $products->total() }} products
                </p>

                <!-- Bulk Delete Button wrapped in Permission Gate -->
                <x-permission-gate permission="delete products" tooltip="Requires 'delete products' permission">
                    <button type="submit" 
                            onclick="return confirm('Delete all selected products?')" 
                            class="inline-flex items-center gap-1.5 bg-rose-600 hover:bg-rose-700 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold shadow-sm transition">
                        <span>🗑️</span>
                        <span>Delete Selected</span>
                    </button>
                </x-permission-gate>
            </div>

            <!-- Main Products Table -->
            <div class="bg-white shadow-sm border border-gray-200 rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-200 text-xs font-bold uppercase text-gray-500 font-mono">
                            <tr>
                                <th class="p-3.5 text-center w-10">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th class="p-3.5">ID</th>
                                <th class="p-3.5 min-w-[200px]">Product Name</th>
                                <th class="p-3.5">Selling Price</th>
                                
                                <!-- Sensitive Masked Columns -->
                                <th class="p-3.5 bg-amber-50/50">
                                    <div class="flex items-center gap-1 text-amber-900">
                                        <span>Cost Price</span>
                                        <span class="text-[10px]" title="Field-Level Restricted">🔒</span>
                                    </div>
                                </th>
                                <th class="p-3.5 bg-amber-50/50">
                                    <div class="flex items-center gap-1 text-amber-900">
                                        <span>Profit Margin</span>
                                        <span class="text-[10px]" title="Field-Level Restricted">🔒</span>
                                    </div>
                                </th>
                                <th class="p-3.5 bg-amber-50/50">
                                    <div class="flex items-center gap-1 text-amber-900">
                                        <span>Supplier</span>
                                        <span class="text-[10px]" title="Field-Level Restricted">🔒</span>
                                    </div>
                                </th>

                                <th class="p-3.5">Created</th>
                                <th class="p-3.5 text-right min-w-[150px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($products as $product)
                                <tr class="hover:bg-gray-50/80 transition">
                                    <td class="p-3.5 text-center">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="p-3.5 font-mono text-xs text-gray-500">
                                        #{{ $product->id }}
                                    </td>
                                    <td class="p-3.5 font-semibold text-gray-800">
                                        {{ $product->name }}
                                    </td>
                                    <td class="p-3.5 font-bold text-emerald-600 font-mono">
                                        ₹{{ number_format($product->price, 2) }}
                                    </td>

                                    <!-- Sensitive Cost Price (Masked if unauthorized) -->
                                    <td class="p-3.5 bg-amber-50/30 font-mono">
                                        <x-masked-data permission="view product costs" :value="'₹' . number_format($product->cost_price ?? ($product->price * 0.7), 2)" />
                                    </td>

                                    <!-- Sensitive Profit Margin (Masked if unauthorized) -->
                                    <td class="p-3.5 bg-amber-50/30 font-mono">
                                        <x-masked-data permission="view product costs" :value="number_format($product->profit_margin ?? 30.0, 1) . '%'" />
                                    </td>

                                    <!-- Sensitive Supplier Code (Masked if unauthorized) -->
                                    <td class="p-3.5 bg-amber-50/30 text-xs font-mono">
                                        <x-masked-data permission="view product costs" :value="$product->supplier_code ?? 'SUP-DEFAULT'" />
                                    </td>

                                    <td class="p-3.5 text-xs text-gray-500 font-mono">
                                        {{ $product->created_at?->format('d M Y') }}
                                    </td>

                                    <!-- Action Buttons wrapped in Permission Gates -->
                                    <td class="p-3.5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <!-- Edit Button -->
                                            <x-permission-gate permission="edit products" tooltip="Requires 'edit products' permission">
                                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center gap-1 bg-amber-500 hover:bg-amber-600 text-white px-2.5 py-1 rounded-lg text-xs font-bold transition shadow-sm">
                                                    <span>✏️</span>
                                                    <span>Edit</span>
                                                </a>
                                            </x-permission-gate>

                                            <!-- Delete Button -->
                                            <x-permission-gate permission="delete products" tooltip="Requires 'delete products' permission">
                                                <button type="button" 
                                                        onclick="if(confirm('Permanently delete product \'{{ $product->name }}\'?')) { document.getElementById('delete-form-{{ $product->id }}').submit(); }"
                                                        class="inline-flex items-center gap-1 bg-rose-600 hover:bg-rose-700 text-white px-2.5 py-1 rounded-lg text-xs font-bold transition shadow-sm">
                                                    <span>🗑️</span>
                                                    <span>Delete</span>
                                                </button>
                                            </x-permission-gate>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="p-8 text-center text-gray-500 font-sans">
                                        No products found matching your search.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($products->hasPages())
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </form>

        <!-- Hidden Delete Forms for row actions -->
        @foreach($products as $product)
            <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>

    <!-- Select All Checkbox Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.product-checkbox');

            if (selectAll) {
                selectAll.addEventListener('change', (e) => {
                    checkboxes.forEach(cb => cb.checked = e.target.checked);
                });
            }
        });
    </script>
</x-app-layout>