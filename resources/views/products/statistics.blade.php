<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <div>

                <h2 class="font-semibold text-xl text-gray-800">
                    Product Statistics
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Overview of product pricing and recent products.
                </p>

            </div>

            <a
                href="{{ route('products.index') }}"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded"
            >
                Back to Products
            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8">

                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Total Products
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        {{ $statistics['total'] }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Average Price
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        ₹{{ number_format($statistics['average_price'] ?? 0, 2) }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Lowest Price
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        ₹{{ number_format($statistics['minimum_price'] ?? 0, 2) }}
                    </p>

                </div>


                <div class="bg-white shadow-sm rounded-lg p-6">

                    <p class="text-sm text-gray-500">
                        Highest Price
                    </p>

                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        ₹{{ number_format($statistics['maximum_price'] ?? 0, 2) }}
                    </p>

                </div>

            </div>


            {{-- Latest --}}
            <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-8">

                <div class="p-6 border-b">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Latest Products
                    </h3>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="p-4 text-left border-b">
                                    ID
                                </th>

                                <th class="p-4 text-left border-b">
                                    Name
                                </th>

                                <th class="p-4 text-left border-b">
                                    Price
                                </th>

                                <th class="p-4 text-left border-b">
                                    Created
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($latestProducts as $product)

                                <tr>

                                    <td class="p-4 border-b">
                                        {{ $product->id }}
                                    </td>

                                    <td class="p-4 border-b">
                                        {{ $product->name }}
                                    </td>

                                    <td class="p-4 border-b">
                                        ₹{{ number_format($product->price, 2) }}
                                    </td>

                                    <td class="p-4 border-b">
                                        {{ $product->created_at?->format('d M Y h:i A') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="p-8 text-center text-gray-500"
                                    >
                                        No products available.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Expensive / Cheapest --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                    <div class="p-6 border-b">

                        <h3 class="text-lg font-semibold">
                            Most Expensive Products
                        </h3>

                    </div>

                    @foreach($mostExpensive as $product)

                        <div class="flex justify-between p-4 border-b">

                            <span>
                                {{ $product->name }}
                            </span>

                            <span class="font-semibold">
                                ₹{{ number_format($product->price, 2) }}
                            </span>

                        </div>

                    @endforeach

                </div>


                <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                    <div class="p-6 border-b">

                        <h3 class="text-lg font-semibold">
                            Cheapest Products
                        </h3>

                    </div>

                    @foreach($cheapest as $product)

                        <div class="flex justify-between p-4 border-b">

                            <span>
                                {{ $product->name }}
                            </span>

                            <span class="font-semibold">
                                ₹{{ number_format($product->price, 2) }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    </div>

</x-app-layout>