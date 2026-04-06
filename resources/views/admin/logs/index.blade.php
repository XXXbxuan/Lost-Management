<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('System Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Action History (Traceability)</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 text-sm">
                            <thead>
                                <tr class="bg-gray-800 text-white uppercase leading-normal">
                                    <th class="py-3 px-6 text-left">Time</th>
                                    <th class="py-3 px-6 text-left">Admin</th>
                                    <th class="py-3 px-6 text-left">Action</th>
                                    <th class="py-3 px-6 text-left">Target Staff</th>
                                    <th class="py-3 px-6 text-left">Details</th>
                                </tr>
                            </thead>

                            <tbody class="text-gray-600 font-light">
                                @forelse ($logs as $log)
                                    @php
                                        $details = filled($log->details)
                                            ? array_filter(array_map('trim', explode(',', $log->details)))
                                            : [];
                                    @endphp

                                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                                        <td class="py-3 px-6 text-left whitespace-nowrap font-bold">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>

                                        <td class="py-3 px-6 text-left">
                                            {{ $log->admin_name }}
                                        </td>

                                        <td class="py-3 px-6 text-left">
                                            <span class="bg-gray-200 text-gray-800 py-1 px-3 rounded-full text-xs font-bold uppercase">
                                                {{ $log->action_type }}
                                            </span>
                                        </td>

                                        <td class="py-3 px-6 text-left font-bold">
                                            {{ $log->target_name }}
                                        </td>

                                        <td class="py-3 px-6 text-left text-xs">
                                            @if (count($details))
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach ($details as $detail)
                                                        <li>{{ $detail }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 text-center">
                                            No logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>