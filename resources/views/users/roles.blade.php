<x-app-layout>
<div class="p-6">
    <h2 class="text-xl font-bold mb-4">Manage User Roles</h2>

    @if(session('success'))
        <div class="bg-green-200 p-2 mb-3">{{ session('success') }}</div>
    @endif

    <table class="w-full border">
        <tr class="bg-gray-100">
            <th class="p-2 border">Name</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Role</th>
            <th class="p-2 border">Action</th>
        </tr>

        @foreach($users as $user)
            <tr>
                <td class="p-2 border">{{ $user->name }}</td>
                <td class="p-2 border">{{ $user->email }}</td>
                <td class="p-2 border">{{ $user->getRoleNames()->first() }}</td>
                <td class="p-2 border">
                    <form method="POST" action="{{ route('user.roles.update', $user) }}">
                        @csrf
                        <select name="role" class="border p-1">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        <button class="bg-blue-600 text-white px-2 py-1 ml-2">Update</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </table>
</div>
</x-app-layout>
