<x-app-layout>

    <x-slot name="header">

        <div>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Access Activity Log
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Track role and permission changes.
            </p>

        </div>

    </x-slot>

    <div class="py-8">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Search --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <form
                    method="GET"
                    action="{{ route('access.activities') }}"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4"
                >

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search activity..."
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                    </div>

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Action
                        </label>

                        <select
                            name="action"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                            <option value="">
                                All Actions
                            </option>

                            @foreach($actions as $availableAction)

                                <option
                                    value="{{ $availableAction }}"
                                    @selected($action === $availableAction)
                                >
                                    {{ ucwords(str_replace('_', ' ', $availableAction)) }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="flex items-end gap-2">

                        <button
                            type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md"
                        >
                            Filter
                        </button>

                        <a
                            href="{{ route('access.activities') }}"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-5 py-2 rounded-md"
                        >
                            Reset
                        </a>

                    </div>

                </form>

            </div>

            {{-- Activity Table --}}
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

                <div class="p-6 border-b">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Permission & Role Changes
                    </h3>

                    <p class="text-sm text-gray-500">
                        {{ $activities->total() }} activity records
                    </p>

                </div>

                <div class="overflow-x-auto">

                    <table class="w-full">

                        <thead class="bg-gray-100">

                            <tr>

                                <th class="text-left p-4 border-b">
                                    Date & Time
                                </th>

                                <th class="text-left p-4 border-b">
                                    Performed By
                                </th>

                                <th class="text-left p-4 border-b">
                                    Action
                                </th>

                                <th class="text-left p-4 border-b">
                                    Description
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($activities as $activity)

                                <tr class="hover:bg-gray-50">

                                    <td class="p-4 border-b text-sm text-gray-600 whitespace-nowrap">

                                        {{ $activity->created_at->format('d M Y') }}

                                        <div class="text-xs text-gray-400">
                                            {{ $activity->created_at->format('h:i A') }}
                                        </div>

                                    </td>

                                    <td class="p-4 border-b">

                                        @if($activity->actor)

                                            <div class="font-medium text-gray-800">
                                                {{ $activity->actor->name }}
                                            </div>

                                            <div class="text-xs text-gray-500">
                                                {{ $activity->actor->email }}
                                            </div>

                                        @else

                                            <span class="text-gray-400">
                                                Deleted User
                                            </span>

                                        @endif

                                    </td>

                                    <td class="p-4 border-b">

                                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucwords(str_replace('_', ' ', $activity->action)) }}
                                        </span>

                                    </td>

                                    <td class="p-4 border-b text-sm text-gray-700">
                                        {{ $activity->description }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="p-8 text-center text-gray-500"
                                    >
                                        No activity records found.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($activities->hasPages())

                    <div class="p-6 border-t">
                        {{ $activities->links() }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>