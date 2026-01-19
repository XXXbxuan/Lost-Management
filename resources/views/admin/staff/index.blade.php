<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <form method="GET" action="{{ route('admin.staff.index') }}" class="flex">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search name or email..." 
                                   class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-r-md hover:bg-gray-700 transition">
                                Search
                            </button>
                        </form>

                        <a href="{{ route('admin.staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition font-bold">
                            + Add New Staff
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">Staff ID</th>
                                    <th class="py-3 px-6 text-left">Name</th>
                                    <th class="py-3 px-6 text-left">Email / Username</th>
                                    <th class="py-3 px-6 text-center">Contact</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($staffMembers as $staff)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-left whitespace-nowrap font-bold">
                                            #{{ $staff->staff_id }}
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            {{ $staff->name }}<br>
                                            <span class="text-xs text-gray-400">{{ $staff->department }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-left">
                                            <div class="flex flex-col">
                                                <span class="font-bold">{{ $staff->user->email ?? 'N/A' }}</span>
                                                <span class="text-xs">{{ $staff->user->username ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            {{ $staff->contact_number ?? '-' }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="{{ $staff->status === 'Active' ? 'bg-green-200 text-green-600' : 'bg-red-200 text-red-600' }} py-1 px-3 rounded-full text-xs font-bold uppercase">
                                                {{ $staff->status }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('admin.staff.edit', $staff->staff_id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-xs">
                                                    Manage
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-500">
                                            No staff members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $staffMembers->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>