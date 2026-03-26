<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Found Items Management
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
                    
                    {{-- 顶部标题和按钮 --}}
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">Found Items List</h3>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('staff.inventory.index') }}"
                            class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                Inventory Map
                            </a>

                            <a href="{{ route('staff.found-items.create') }}" 
                            class="bg-black text-white px-4 py-2 rounded-md font-bold hover:bg-gray-800 transition shadow-md no-underline flex items-center gap-2 text-sm">
                                <span>+</span> Register New Item
                            </a>
                        </div>
                    </div>

                    {{-- 🔥🔥🔥 1. 状态筛选 Tabs (修复了注释报错问题) 🔥🔥🔥 --}}
                    <div class="flex flex-wrap gap-2 mb-6 border-b pb-4">
                        @php
                            $filters = ['All', 'Unclaimed', 'Matched', 'Claimed'];
                        @endphp

                        @foreach($filters as $filter)
                            <a href="{{ route('staff.found-items.index', ['status' => $filter]) }}"
                               class="px-5 py-2 rounded-full text-xs font-bold border transition duration-200 no-underline shadow-sm
                               {{ $status === $filter 
                                   ? 'bg-gray-800 text-white border-gray-800'
                                   : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'
                               }}">
                               {{ $filter }}
                            </a>
                        @endforeach
                    </div>

                    {{-- 表格内容 --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 uppercase text-xs leading-normal">
                                    <th class="py-3 px-6 text-left">Image</th>
                                    <th class="py-3 px-6 text-left">Item Details</th>
                                    <th class="py-3 px-6 text-left">Location</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm font-light">
                                @forelse($foundItems as $item)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                        {{-- Image Column --}}
                                        <td class="py-3 px-6 text-left">
                                            @if($item->image_path)
                                                <img src="{{ asset('storage/' . $item->image_path) }}" class="w-16 h-16 object-cover border rounded shadow-sm">
                                            @else
                                                <div class="w-16 h-16 bg-gray-100 text-gray-400 flex items-center justify-center text-xs rounded border italic">
                                                    No Img
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Details Column --}}
                                        <td class="py-3 px-6 text-left">
                                            <div class="font-bold text-gray-800">{{ $item->item_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->category }} | {{ $item->color }}</div>
                                            @if($item->brand)
                                                <div class="text-xs text-gray-400 mt-0.5">Brand: {{ $item->brand }}</div>
                                            @endif
                                        </td>

                                        {{-- Location Column --}}
                                        <td class="py-3 px-6 text-left">
                                            <div class="font-semibold text-gray-700">{{ $item->found_location }}</div>
                                            <div class="text-xs text-gray-400">{{ $item->found_time }}</div>
                                            @if($item->storage_location)
                                                <div class="text-[10px] text-indigo-500 font-bold mt-1 bg-indigo-50 inline-block px-1 rounded border border-indigo-100">
                                                    Store: {{ $item->storage_location }}
                                                </div>
                                            @endif
                                        </td>

                                        {{-- Status Column (自动变色) --}}
                                        <td class="py-3 px-6 text-center">
                                            @php
                                                $statusClass = match($item->status) {
                                                    'Matched' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                    'Claimed' => 'bg-green-100 text-green-800 border-green-200',
                                                    'Unclaimed' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                    default => 'bg-gray-100 text-gray-800 border-gray-200'
                                                };
                                            @endphp
                                            <span class="{{ $statusClass }} py-1 px-3 rounded-full text-[10px] font-black uppercase tracking-wider border">
                                                {{ $item->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 text-center text-gray-400 italic bg-gray-50">
                                            No items found in this category. 
                                            <br>
                                            <a href="{{ route('staff.found-items.create') }}" class="text-blue-600 hover:underline font-bold text-xs mt-2 inline-block">
                                                Register a new item?
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- 分页 --}}
                    <div class="mt-4">
                        {{ $foundItems->appends(['status' => $status])->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>