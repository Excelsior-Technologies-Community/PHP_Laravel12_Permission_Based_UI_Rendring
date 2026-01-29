<x-app-layout>
<div class="p-6">
    <form method="POST" action="{{ route('products.store') }}">
        @csrf
        <input name="name" placeholder="Name" class="border p-2"><br><br>
        <input name="price" placeholder="Price" class="border p-2"><br><br>
        <button class="bg-green-600 text-white px-4 py-2">Save</button>
    </form>
</div>
</x-app-layout>
