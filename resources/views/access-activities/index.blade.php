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


            {{-- Filters --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6 mb-6">

                <form
                    method="GET"
                    action="{{ route('access.activities') }}"
                    class="grid grid-cols-1 md:grid-cols-5 gap-4"
                >

                    {{-- Search --}}
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


                    {{-- Action --}}
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


                    {{-- Actor --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Performed By
                        </label>

                        <select
                            name="actor_id"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                            <option value="">
                                All Users
                            </option>

                            @foreach($actors as $actor)

                                <option
                                    value="{{ $actor->id }}"
                                    @selected((string) $actorId === (string) $actor->id)
                                >
                                    {{ $actor->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Date From --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            value="{{ $dateFrom }}"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                    </div>


                    {{-- Date To --}}
                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            value="{{ $dateTo }}"
                            class="w-full border border-gray-300 rounded-md px-4 py-2"
                        >

                    </div>


                    <div class="md:col-span-5 flex gap-2">

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


            {{-- Table --}}
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
                                    Target
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


                                    <td class="p-4 border-b text-sm">

                                        <div class="font-medium">
                                            {{ ucfirst($activity->target_type) }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            ID: {{ $activity->target_id }}
                                        </div>

                                    </td>


                                    <td class="p-4 border-b text-sm text-gray-700">

                                        {{ $activity->description }}

                                        @if($activity->metadata)

                                            <details class="mt-2">

                                                <summary class="cursor-pointer text-xs text-blue-600">
                                                    View Details
                                                </summary>

                                                <pre class="text-xs bg-gray-100 p-2 rounded mt-2 overflow-x-auto">{{ json_encode($activity->metadata, JSON_PRETTY_PRINT) }}</pre>

                                            </details>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
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