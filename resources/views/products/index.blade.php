<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <h2 class="font-semibold text-xl text-gray-800">
                Products
            </h2>

        </div>

    </x-slot>

    <div class="p-6">

        {{-- Success --}}
        @if(session('success'))

            <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-5">
                {{ session('success') }}
            </div>

        @endif

        {{-- Validation Errors --}}
        @if($errors->any())

            <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-5">

                <ul class="list-disc ml-5">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        {{-- Add Product --}}
        @can('create products')

            <a
                href="{{ route('products.create') }}"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
            >
                + Add Product
            </a>

        @endcan

        {{-- Product Table --}}
        <div class="bg-white shadow-sm rounded-lg mt-5 overflow-hidden">

            <table class="w-full border-collapse">

                <thead>

                    <tr class="bg-gray-100">

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
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($products as $product)

                        <tr class="hover:bg-gray-50">

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

                                        <form
                                            action="{{ route('products.destroy', $product) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this product?');"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-white"
                                            >
                                                Delete
                                            </button>

                                        </form>

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

    </div>

</x-app-layout>