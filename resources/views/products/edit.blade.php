<x-app-layout>
<div class="p-6">
    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')
        <input name="name" value="{{ $product->name }}" class="border p-2"><br><br>
        <input name="price" value="{{ $product->price }}" class="border p-2"><br><br>
        <button class="bg-blue-600 text-white px-4 py-2">Update</button>
    </form>
</div>
</x-app-layout>
