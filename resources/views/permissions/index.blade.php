<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Permission Management
                </h2>


            <p class="text-sm text-gray-500 mt-1">
                Create permissions and control which permissions are assigned to each role.
            </p>
        </div>
    </div>
</x-slot>

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Success Message --}}
        @if (session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                {{ session('error') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                <div class="font-semibold mb-2">
                    Please fix the following errors:
                </div>

                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Create Permission --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Create Permission
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Add a new permission that can be assigned to roles.
                        </p>
                    </div>

                    <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <span class="text-indigo-600 text-lg">+</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('permissions.store') }}">
                    @csrf

                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <label
                                for="name"
                                class="block text-sm font-medium text-gray-700 mb-1"
                            >
                                Permission Name
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="Example: export products"
                                required
                                class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            >
                        </div>

                        <div class="sm:self-end">
                            <button
                                type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                            >
                                Create Permission
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Permission Search --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
            <div class="p-6">

                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Search Permissions
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Search permissions by name or filter them by guard.
                        </p>
                    </div>

                    <div class="text-sm text-gray-500">
                        {{ $permissions->count() }} permission(s) found
                    </div>
                </div>

                <form
                    method="GET"
                    action="{{ route('permissions.index') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >
                    <div>
                        <label
                            for="permission_search"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Permission Search
                        </label>

                        <input
                            id="permission_search"
                            name="permission_search"
                            type="text"
                            value="{{ $permissionSearch ?? request('permission_search') }}"
                            placeholder="Search permission..."
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                    </div>

                    <div>
                        <label
                            for="permission_guard"
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Guard
                        </label>

                        <select
                            id="permission_guard"
                            name="permission_guard"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        >
                            <option value="">All Guards</option>

                            @foreach ($guards as $guard)
                                <option
                                    value="{{ $guard }}"
                                    @selected(($permissionGuard ?? request('permission_guard')) === $guard)
                                >
                                    {{ $guard }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition"
                        >
                            Search
                        </button>

                        <a
                            href="{{ route('permissions.index') }}"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-100 border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 transition"
                        >
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Permissions List --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
            <div class="p-6">

                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Available Permissions
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Manage permissions available in the application.
                        </p>
                    </div>

                    <div class="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-sm font-medium">
                        {{ $permissions->count() }} Total
                    </div>
                </div>

                @if ($permissions->isEmpty())

                    <div class="text-center py-12">
                        <div class="text-4xl mb-3">
                            🔍
                        </div>

                        <h4 class="text-lg font-semibold text-gray-900">
                            No permissions found
                        </h4>

                        <p class="text-sm text-gray-500 mt-1">
                            Try changing your search or guard filter.
                        </p>
                    </div>

                @else

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                        @foreach ($permissions as $permission)

                            <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md transition">

                                <div class="flex items-start justify-between gap-3">

                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900 break-words">
                                            {{ $permission->name }}
                                        </div>

                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                Guard: {{ $permission->guard_name }}
                                            </span>
                                        </div>
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('permissions.destroy', $permission) }}"
                                        onsubmit="return confirm('Are you sure you want to delete this permission?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-xs font-semibold transition"
                                        >
                                            Delete
                                        </button>
                                    </form>

                                </div>
                            </div>

                        @endforeach

                    </div>

                @endif

            </div>
        </div>

        {{-- Role Permissions --}}
        <div class="space-y-6">

            <div>
                <h3 class="text-xl font-semibold text-gray-900">
                    Role Permissions
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    Select the permissions that should be assigned to each role.
                </p>
            </div>

            @forelse ($roles as $role)

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">

                    <div class="p-6">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">

                            <div>
                                <h4 class="text-lg font-semibold text-gray-900">
                                    {{ $role->name }}
                                </h4>

                                <p class="text-sm text-gray-500 mt-1">
                                    Select permissions for this role.
                                </p>
                            </div>

                            <div class="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold">
                                {{ $role->permissions->count() }} assigned
                            </div>

                        </div>

                        <form
                            method="POST"
                            action="{{ route('permissions.roles.update', $role) }}"
                        >
                            @csrf
                            @method('PUT')

                            @if ($permissions->isEmpty())

                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-4 text-sm text-gray-600">
                                    No permissions are available to assign.
                                </div>

                            @else

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                                    @foreach ($permissions as $permission)

                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition"
                                        >
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission->id }}"
                                                @checked($role->permissions->contains('id', $permission->id))
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            >

                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-gray-900 break-words">
                                                    {{ $permission->name }}
                                                </div>

                                                <div class="text-xs text-gray-500">
                                                    {{ $permission->guard_name }}
                                                </div>
                                            </div>
                                        </label>

                                    @endforeach

                                </div>

                            @endif

                            <div class="mt-6 flex justify-end">

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition"
                                >
                                    Update {{ $role->name }}
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            @empty

                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                    No roles found.
                </div>

            @endforelse

        </div>

    </div>
</div>


</x-app-layout>
