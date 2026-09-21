<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                ➕ Add New Product
            </h2>
            <a href="{{ route('products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-semibold transition">
                ← Back to Products
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="bg-white shadow-sm border border-gray-200 rounded-2xl p-6 sm:p-8">

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl mb-6 text-sm">
                    <ul class="list-disc ml-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                @csrf

                <!-- Basic Product Information -->
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-400 font-mono mb-4 pb-2 border-b border-gray-100">
                        1. Public Product Info
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Product Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Apple iPad Air 11-inch M2" class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">
                                Selling Price (₹) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required placeholder="e.g. 59900.00" class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <!-- Sensitive Financial & Supplier Information (Field-Level Gate) -->
                @php
                    $canEditCosts = auth()->check() && auth()->user()->can('edit product costs');
                    $policy = session('ui_policy', 'disabled_lock');
                @endphp

                @if($canEditCosts || $policy === 'disabled_lock')
                    <div class="pt-4">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100 mb-4">
                            <h3 class="text-sm font-bold uppercase tracking-wider text-amber-800 font-mono flex items-center gap-1.5">
                                <span>🔒</span>
                                <span>2. Sensitive Financial & Supplier Details</span>
                            </h3>

                            @if(!$canEditCosts)
                                <span class="text-xs font-mono text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full font-bold">
                                    🔒 Locked (No 'edit product costs' permission)
                                </span>
                            @else
                                <span class="text-xs font-mono text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full font-bold">
                                    ✓ Authorized to Edit
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-amber-50/40 p-4 rounded-xl border border-amber-200/60 {{ !$canEditCosts ? 'opacity-60 select-none' : '' }}">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1 flex items-center justify-between">
                                    <span>Cost Price (₹)</span>
                                    @if(!$canEditCosts) <span class="text-rose-500 text-[10px] font-mono">🔒 Locked</span> @endif
                                </label>
                                <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" min="0" placeholder="e.g. 42000.00" 
                                       {{ !$canEditCosts ? 'disabled readonly' : '' }}
                                       class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$canEditCosts ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                <p class="text-[11px] text-gray-500 mt-1">Acquisition price used to compute gross profit margin.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1 flex items-center justify-between">
                                    <span>Supplier Code</span>
                                    @if(!$canEditCosts) <span class="text-rose-500 text-[10px] font-mono">🔒 Locked</span> @endif
                                </label>
                                <input type="text" name="supplier_code" value="{{ old('supplier_code') }}" placeholder="e.g. SUP-APPLE-DIRECT" 
                                       {{ !$canEditCosts ? 'disabled readonly' : '' }}
                                       class="w-full text-sm border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ !$canEditCosts ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                                <p class="text-[11px] text-gray-500 mt-1">Confidential vendor procurement identifier.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </a>

                    <x-permission-gate permission="create products" tooltip="Requires 'create products' permission">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition shadow-md shadow-indigo-600/20">
                            Save Product
                        </button>
                    </x-permission-gate>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>