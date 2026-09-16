<x-app-layout>

    <x-slot name="header">

        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                User Access Management
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Search users, filter by role and manage access.
            </p>
        </div>

    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Success --}}
            @if(session('success'))

                <div class="mb-5 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>

            @endif

            {{-- Error --}}
            @if(session('error'))

                <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>

            @endif

            {{-- Search & Filter --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <form
                    method="GET"
                    action="{{ route('user.roles') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Search User
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Name or email"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Filter by Role
                        </label>

                        <select
                            name="role"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                            <option value="">
                                All Roles
                            </option>

                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->name }}"
                                    @selected($roleFilter === $role->name)
                                >
                                    {{ ucfirst($role->name) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="flex items-end gap-2">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md"
                        >
                            Search
                        </button>

                        <a
                            href="{{ route('user.roles') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-md"
                        >
                            Reset
                        </a>

                    </div>

                </form>

            </div>

            {{-- User Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Users
                    </h3>

                    <p class="text-sm text-gray-500">
                        {{ $users->total() }} users found
                    </p>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="text-left p-4 border-b">
                                    Name
                                </th>

                                <th class="text-left p-4 border-b">
                                    Email
                                </th>

                                <th class="text-left p-4 border-b">
                                    Current Role
                                </th>

                                <th class="text-left p-4 border-b">
                                    Permissions
                                </th>

                                <th class="text-left p-4 border-b">
                                    Change Role
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($users as $user)

                                <tr class="hover:bg-gray-50">

                                    <td class="p-4 border-b">

                                        <div class="font-medium text-gray-800">
                                            {{ $user->name }}
                                        </div>

                                        @if($user->id === auth()->id())

                                            <span class="text-xs text-blue-600">
                                                You
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-4 border-b text-gray-600">
                                        {{ $user->email }}
                                    </td>

                                    <td class="p-4 border-b">

                                        @php
                                            $currentRole = $user->getRoleNames()->first();
                                        @endphp

                                        @if($currentRole)

                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ ucfirst($currentRole) }}
                                            </span>

                                        @else

                                            <span class="text-gray-400">
                                                No role
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-4 border-b">

                                        @php
                                            $permissions = $user->getAllPermissions();
                                        @endphp

                                        <div class="text-sm text-gray-700">
                                            {{ $permissions->count() }}
                                            permissions
                                        </div>

                                        @if($permissions->count())

                                            <div class="text-xs text-gray-500 mt-1 max-w-xs">
                                                {{ $permissions->pluck('name')->join(', ') }}
                                            </div>

                                        @endif

                                    </td>

                                    <td class="p-4 border-b">

                                        <form
                                            method="POST"
                                            action="{{ route('user.roles.update', $user) }}"
                                            class="flex gap-2"
                                        >

                                            @csrf

                                            <select
                                                name="role"
                                                class="border border-gray-300 rounded-md px-3 py-2 text-sm"
                                            >

                                                @foreach($roles as $role)

                                                    <option
                                                        value="{{ $role->name }}"
                                                        @selected($user->hasRole($role->name))
                                                    >
                                                        {{ ucfirst($role->name) }}
                                                    </option>

                                                @endforeach

                                            </select>

                                            <button
                                                type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm"
                                            >
                                                Update
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="p-8 text-center text-gray-500"
                                    >
                                        No users found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($users->hasPages())

                    <div class="p-6 border-t">
                        {{ $users->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>