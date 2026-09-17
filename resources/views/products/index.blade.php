<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">

            <div>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Products
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Search, filter, sort and manage products.
                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                @can('view products')

                    <a
                        href="{{ route('products.statistics') }}"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
                    >
                        Statistics
                    </a>

                @endcan

                @can('view products')

                    <a
                        href="{{ route('products.export') }}"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
                    >
                        Export CSV
                    </a>

                @endcan

                @can('create products')

                    <a
                        href="{{ route('products.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                    >
                        + Add Product
                    </a>

                @endcan

            </div>

        </div>

    </x-slot>


    <div class="p-6">

        {{-- Success --}}
        @if(session('success'))

            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-5">

                {{ session('success') }}

            </div>

        @endif


        {{-- Validation errors --}}
        @if($errors->any())

            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-5">

                <ul class="list-disc ml-5">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

            <div class="bg-white shadow-sm rounded-lg p-5">

                <p class="text-sm text-gray-500">
                    Total Products
                </p>

                <p class="text-2xl font-bold text-gray-800 mt-2">
                    {{ $statistics['total'] }}
                </p>

            </div>


            <div class="bg-white shadow-sm rounded-lg p-5">

                <p class="text-sm text-gray-500">
                    Average Price
                </p>

                <p class="text-2xl font-bold text-gray-800 mt-2">
                    ₹{{ number_format($statistics['average_price'] ?? 0, 2) }}
                </p>

            </div>


            <div class="bg-white shadow-sm rounded-lg p-5">

                <p class="text-sm text-gray-500">
                    Minimum Price
                </p>

                <p class="text-2xl font-bold text-gray-800 mt-2">
                    ₹{{ number_format($statistics['minimum_price'] ?? 0, 2) }}
                </p>

            </div>


            <div class="bg-white shadow-sm rounded-lg p-5">

                <p class="text-sm text-gray-500">
                    Maximum Price
                </p>

                <p class="text-2xl font-bold text-gray-800 mt-2">
                    ₹{{ number_format($statistics['maximum_price'] ?? 0, 2) }}
                </p>

            </div>

        </div>


        {{-- Search / Filters --}}
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">

            <form
                method="GET"
                action="{{ route('products.index') }}"
                class="grid grid-cols-1 md:grid-cols-5 gap-4"
            >

                {{-- Search --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Product name..."
                        class="w-full border border-gray-300 rounded-md px-4 py-2"
                    >

                </div>


                {{-- Minimum --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Min Price
                    </label>

                    <input
                        type="number"
                        name="min_price"
                        value="{{ $minPrice }}"
                        min="0"
                        step="0.01"
                        placeholder="0"
                        class="w-full border border-gray-300 rounded-md px-4 py-2"
                    >

                </div>


                {{-- Maximum --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Max Price
                    </label>

                    <input
                        type="number"
                        name="max_price"
                        value="{{ $maxPrice }}"
                        min="0"
                        step="0.01"
                        placeholder="999999"
                        class="w-full border border-gray-300 rounded-md px-4 py-2"
                    >

                </div>


                {{-- Sort --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Sort By
                    </label>

                    <select
                        name="sort"
                        class="w-full border border-gray-300 rounded-md px-4 py-2"
                    >

                        <option
                            value="created_at"
                            @selected($sort === 'created_at')
                        >
                            Latest
                        </option>

                        <option
                            value="name"
                            @selected($sort === 'name')
                        >
                            Name
                        </option>

                        <option
                            value="price"
                            @selected($sort === 'price')
                        >
                            Price
                        </option>

                        <option
                            value="id"
                            @selected($sort === 'id')
                        >
                            ID
                        </option>

                    </select>

                </div>


                {{-- Direction --}}
                <div>

                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Direction
                    </label>

                    <select
                        name="direction"
                        class="w-full border border-gray-300 rounded-md px-4 py-2"
                    >

                        <option
                            value="desc"
                            @selected($direction === 'desc')
                        >
                            Descending
                        </option>

                        <option
                            value="asc"
                            @selected($direction === 'asc')
                        >
                            Ascending
                        </option>

                    </select>

                </div>


                <div class="md:col-span-5 flex gap-2">

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md"
                    >
                        Apply Filters
                    </button>

                    <a
                        href="{{ route('products.index') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-md"
                    >
                        Reset
                    </a>

                </div>

            </form>

        </div>


        {{-- Bulk Delete --}}
        @can('delete products')

            <form
                method="POST"
                action="{{ route('products.bulk-delete') }}"
                id="bulkDeleteForm"
            >

                @csrf

                @method('DELETE')


                <div class="flex justify-between items-center mb-3">

                    <p class="text-sm text-gray-500">
                        {{ $products->total() }} products found
                    </p>

                    <button
                        type="submit"
                        onclick="return confirm('Delete all selected products?')"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded"
                    >
                        Delete Selected
                    </button>

                </div>


                {{-- Product Table --}}
                <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                    <div class="overflow-x-auto">

                        <table class="w-full border-collapse">

                            <thead>

                                <tr class="bg-gray-100">

                                    <th class="p-3 border text-center">

                                        <input
                                            type="checkbox"
                                            id="selectAll"
                                            class="rounded"
                                        >

                                    </th>

                                    <th class="p-3 border text-left">
                                        ID
                                    </th>

                                    <th class="p-3 border text-left">
                                        Name
                                    </th>

                                    <th class="p-3 border text-left">
                                        Price
                                    </th>

                                    <th class="p-3 border text-left">
                                        Created
                                    </th>

                                    <th class="p-3 border text-left">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse($products as $product)

                                    <tr class="hover:bg-gray-50">

                                        <td class="p-3 border text-center">

                                            <input
                                                type="checkbox"
                                                name="product_ids[]"
                                                value="{{ $product->id }}"
                                                class="product-checkbox rounded"
                                            >

                                        </td>


                                        <td class="p-3 border">
                                            {{ $product->id }}
                                        </td>


                                        <td class="p-3 border">
                                            {{ $product->name }}
                                        </td>


                                        <td class="p-3 border">
                                            ₹{{ number_format($product->price, 2) }}
                                        </td>


                                        <td class="p-3 border text-sm text-gray-600">
                                            {{ $product->created_at?->format('d M Y') }}
                                        </td>


                                        <td class="p-3 border">

                                            <div class="flex gap-2">

                                                @can('edit products')

                                                    <a
                                                        href="{{ route('products.edit', $product) }}"
                                                        class="bg-yellow-500 hover:bg-yellow-600 px-3 py-1 rounded text-white"
                                                    >
                                                        Edit
                                                    </a>

                                                @endcan


                                                @can('delete products')

                                                    <button
                                                        type="button"
                                                        onclick="deleteSingleProduct({{ $product->id }})"
                                                        class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white"
                                                    >
                                                        Delete
                                                    </button>

                                                @endcan


                                                @cannot('edit products')

                                                    @cannot('delete products')

                                                        <span class="text-gray-400 text-sm">
                                                            No actions
                                                        </span>

                                                    @endcannot

                                                @endcannot

                                            </div>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="6"
                                            class="p-8 text-center text-gray-500"
                                        >
                                            No products found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- Pagination --}}
                    @if($products->hasPages())

                        <div class="p-6 border-t">

                            {{ $products->links() }}

                        </div>

                    @endif

                </div>

            </form>

        @else

            {{-- Read-only table --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-4 border-b text-sm text-gray-500">
                    {{ $products->total() }} products found
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="p-3 border text-left">
                                    ID
                                </th>

                                <th class="p-3 border text-left">
                                    Name
                                </th>

                                <th class="p-3 border text-left">
                                    Price
                                </th>

                                <th class="p-3 border text-left">
                                    Created
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($products as $product)

                                <tr>

                                    <td class="p-3 border">
                                        {{ $product->id }}
                                    </td>

                                    <td class="p-3 border">
                                        {{ $product->name }}
                                    </td>

                                    <td class="p-3 border">
                                        ₹{{ number_format($product->price, 2) }}
                                    </td>

                                    <td class="p-3 border">
                                        {{ $product->created_at?->format('d M Y') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="p-8 text-center text-gray-500"
                                    >
                                        No products found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($products->hasPages())

                    <div class="p-6 border-t">

                        {{ $products->links() }}

                    </div>

                @endif

            </div>

        @endcan

    </div>


    {{-- Hidden single delete forms --}}
    @can('delete products')

        @foreach($products as $product)

            <form
                id="delete-product-{{ $product->id }}"
                method="POST"
                action="{{ route('products.destroy', $product) }}"
                class="hidden"
            >

                @csrf
                @method('DELETE')

            </form>

        @endforeach

    @endcan


    <script>

        document
            .getElementById('selectAll')
            ?.addEventListener('change', function () {

                document
                    .querySelectorAll('.product-checkbox')
                    .forEach(function (checkbox) {

                        checkbox.checked = this.checked;

                    }, this);


            });


        function deleteSingleProduct(id)
        {
            if (
                confirm(
                    'Delete this product?'
                )
            ) {

                document
                    .getElementById(
                        'delete-product-' + id
                    )
                    .submit();

            }
        }

    </script>

</x-app-layout>