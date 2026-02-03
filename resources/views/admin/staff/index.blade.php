<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staff Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 成功提示 --}}
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">

                    {{-- 顶部功能区：搜索 + 添加按钮 --}}
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <form method="GET" action="{{ route('admin.staff.index') }}" class="flex w-full md:w-auto gap-2">
                            <input type="text" name="search" placeholder="Search name or email..." value="{{ request('search') }}" 
                                class="rounded-l-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-64">
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-r-md hover:bg-gray-700 transition font-bold">
                                Search
                            </button>
                        </form>

                        <a href="{{ route('admin.staff.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md font-bold hover:bg-blue-700 transition shadow-md no-underline flex items-center gap-2">
                            <span>+</span> Add New Staff
                        </a>
                    </div>

                    {{-- 表格区域 --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal border-b">
                                    <th class="py-3 px-6 text-left font-bold">Staff ID</th>
                                    <th class="py-3 px-6 text-left font-bold">Name / Dept</th>
                                    <th class="py-3 px-6 text-center font-bold">Role</th>
                                    <th class="py-3 px-6 text-left font-bold">Email / Username</th>
                                    <th class="py-3 px-6 text-left font-bold">Contact</th>
                                    <th class="py-3 px-6 text-center font-bold">Status</th>
                                    <th class="py-3 px-6 text-center font-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($staffMembers as $staff)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        
                                        {{-- Staff ID --}}
                                        <td class="py-3 px-6 text-left whitespace-nowrap font-bold">
                                            #{{ $staff->staff_id }}
                                        </td>

                                        {{-- Name & Department --}}
                                        <td class="py-3 px-6 text-left">
                                            <div class="font-bold text-gray-800 text-base">{{ $staff->name }}</div>
                                            <div class="text-xs text-gray-400 mt-1 uppercase tracking-wide">{{ $staff->department }}</div>
                                        </td>

                                        {{-- Role (角色区分) --}}
                                        <td class="py-3 px-6 text-center">
                                            @if(isset($staff->user->role) && strtolower($staff->user->role) === 'admin')
                                                <span class="bg-purple-100 text-purple-700 py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border border-purple-200 shadow-sm">
                                                    👑 Admin
                                                </span>
                                            @else
                                                <span class="bg-blue-50 text-blue-600 py-1 px-3 rounded-full text-[10px] font-bold uppercase tracking-wider border border-blue-100">
                                                    👤 Staff
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Email & Username --}}
                                        <td class="py-3 px-6 text-left">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-gray-700">{{ $staff->user->email ?? 'N/A' }}</span>
                                                <span class="text-xs text-gray-400 font-mono">{{ $staff->user->username ?? 'N/A' }}</span>
                                            </div>
                                        </td>

                                        {{-- Contact --}}
                                        <td class="py-3 px-6 text-left">
                                            {{ $staff->contact_number ?? '-' }}
                                        </td>

                                        {{-- Status --}}
                                        <td class="py-3 px-6 text-center">
                                            @if($staff->status === 'Active')
                                                <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-[10px] font-bold uppercase tracking-wide border border-green-200">
                                                    Active
                                                </span>
                                            @else
                                                <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-[10px] font-bold uppercase tracking-wide border border-red-200">
                                                    Blocked
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Actions (已删除 Delete 按钮，并修复 ID 问题) --}}
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center">
                                                <a href="{{ route('admin.staff.edit', $staff->staff_id) }}" class="text-blue-600 hover:text-blue-900 font-bold text-xs uppercase hover:underline">
                                                    Manage
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-10 text-center text-gray-400 italic">
                                            No staff members found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- 分页 --}}
                    <div class="mt-4">
                        {{ $staffMembers->withQueryString()->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>