<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Role & Permission Management
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Manage permissions assigned to application roles.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc ml-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Create Permission --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Create New Permission
                </h3>

                <form
                    method="POST"
                    action="{{ route('permissions.store') }}"
                    class="flex flex-col sm:flex-row gap-3"
                >

                    @csrf

                    <input
                        type="text"
                        name="name"
                        placeholder="Example: export products"
                        class="border border-gray-300 rounded-md px-4 py-2 flex-1"
                        required
                    >

                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md"
                    >
                        Create Permission
                    </button>

                </form>

            </div>

            {{-- Permission List --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <div class="flex justify-between items-center mb-4">

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Available Permissions
                        </h3>

                        <p class="text-sm text-gray-500">
                            {{ $permissions->count() }} permissions available
                        </p>
                    </div>

                </div>

                @if($permissions->count())

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">

                        @foreach($permissions as $permission)

                            <div class="border rounded-lg p-4 flex justify-between items-center">

                                <div>
                                    <div class="font-medium text-gray-800">
                                        {{ $permission->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        Guard: {{ $permission->guard_name }}
                                    </div>
                                </div>

                                <form
                                    method="POST"
                                    action="{{ route('permissions.destroy', $permission) }}"
                                    onsubmit="return confirm('Delete this permission?');"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="text-red-600 hover:text-red-800 text-sm"
                                    >
                                        Delete
                                    </button>

                                </form>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="text-gray-500">
                        No permissions found.
                    </p>

                @endif

            </div>

            {{-- Role Permission Assignment --}}
            <div class="space-y-6">

                @foreach($roles as $role)

                    <div class="bg-white shadow-sm sm:rounded-lg p-6">

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-5">

                            <div>

                                <h3 class="text-lg font-semibold text-gray-800">
                                    {{ ucfirst($role->name) }}
                                </h3>

                                <p class="text-sm text-gray-500">
                                    {{ $role->permissions->count() }}
                                    permissions currently assigned
                                </p>

                            </div>

                        </div>

                        <form
                            method="POST"
                            action="{{ route('permissions.roles.update', $role) }}"
                        >

                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                                @foreach($permissions as $permission)

                                    <label class="flex items-center gap-3 border rounded-lg p-3 hover:bg-gray-50 cursor-pointer">

                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            class="rounded border-gray-300 text-blue-600"
                                            @checked($role->hasPermissionTo($permission->name))
                                        >

                                        <span class="text-sm text-gray-700">
                                            {{ $permission->name }}
                                        </span>

                                    </label>

                                @endforeach

                            </div>

                            <div class="flex justify-end">

                                <button
                                    type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md"
                                >
                                    Update {{ ucfirst($role->name) }} Permissions
                                </button>

                            </div>

                        </form>

                    </div>

                @endforeach

            </div>

        </div>

    </div>

</x-app-layout>