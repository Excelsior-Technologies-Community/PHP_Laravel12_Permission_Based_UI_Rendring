<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Products</h2>
    </x-slot>

    <div class="p-6">

        @can('create products')
            <a href="{{ route('products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                Add Product
            </a>
        @endcan

        <table class="mt-4 w-full border">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Price</th>
                    <th class="p-2 border">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td class="p-2 border">{{ $product->id }}</td>
                        <td class="p-2 border">{{ $product->name }}</td>
                        <td class="p-2 border">₹{{ $product->price }}</td>
                        <td class="p-2 border flex gap-2">

                            @can('edit products')
                                <a href="{{ route('products.edit', $product) }}" class="bg-yellow-500 px-2 py-1 rounded text-white">Edit</a>
                            @endcan

                            @can('delete products')
                                <form action="{{ route('products.destroy', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-600 px-2 py-1 rounded text-white">Delete</button>
                                </form>
                            @endcan

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
