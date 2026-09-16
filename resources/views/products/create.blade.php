<x-app-layout>

    <x-slot name="header">

        <h2 class="font-semibold text-xl text-gray-800">
            Add Product
        </h2>

    </x-slot>

    <div class="py-8">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())

                    <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded mb-5">

                        <ul class="list-disc ml-5">

                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>

                @endif

                <form
                    method="POST"
                    action="{{ route('products.store') }}"
                >

                    @csrf

                    <div class="mb-5">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                            placeholder="Enter product name"
                            required
                        >

                    </div>

                    <div class="mb-5">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            value="{{ old('price') }}"
                            step="0.01"
                            min="0"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                            placeholder="Enter product price"
                            required
                        >

                    </div>

                    <div class="flex gap-3">

                        <button
                            type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md"
                        >
                            Save Product
                        </button>

                        <a
                            href="{{ route('products.index') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-md"
                        >
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>