<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Vouchers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                {{-- LEFT: CREATE NEW VOUCHER FORM --}}
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">➕ Add New Voucher</h3>
                        
                        <form action="{{ route('staff.vouchers.store') }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">Voucher Name</label>
                                <input type="text" name="name" class="border-gray-300 rounded-md shadow-sm w-full mt-1" required placeholder="e.g. Free Coffee">
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">Category</label>
                                <select name="category" class="border-gray-300 rounded-md shadow-sm w-full mt-1">
                                    <option value="Food & Beverage">Food & Beverage</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Service">Service</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">Points Cost</label>
                                <input type="number" name="points" class="border-gray-300 rounded-md shadow-sm w-full mt-1" required placeholder="e.g. 500">
                            </div>

                            <div class="mb-4">
                                <label class="block font-medium text-sm text-gray-700">Description</label>
                                <textarea name="description" rows="3" class="border-gray-300 rounded-md shadow-sm w-full mt-1" required placeholder="Terms and conditions..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                                Create Voucher
                            </button>
                        </form>
                    </div>
                </div>

                {{-- RIGHT: LIST OF VOUCHERS --}}
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">🎟️ Active Vouchers</h3>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full border-collapse block md:table">
                                <thead class="block md:table-header-group">
                                    <tr class="border border-gray-200 block md:table-row absolute -top-full md:top-auto -left-full md:left-auto md:relative">
                                        <th class="bg-gray-100 p-2 text-gray-600 font-bold block md:table-cell text-left">Name</th>
                                        <th class="bg-gray-100 p-2 text-gray-600 font-bold block md:table-cell text-left">Category</th>
                                        <th class="bg-gray-100 p-2 text-gray-600 font-bold block md:table-cell text-left">Points</th>
                                        <th class="bg-gray-100 p-2 text-gray-600 font-bold block md:table-cell text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="block md:table-row-group">
                                    @forelse($vouchers as $voucher)
                                        <tr class="bg-white border border-gray-200 md:border-none block md:table-row hover:bg-gray-50">
                                            <td class="p-2 md:border md:border-gray-100 block md:table-cell">
                                                <span class="font-bold text-gray-800">{{ $voucher->name }}</span>
                                            </td>
                                            <td class="p-2 md:border md:border-gray-100 block md:table-cell">
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">{{ $voucher->category }}</span>
                                            </td>
                                            <td class="p-2 md:border md:border-gray-100 block md:table-cell font-bold text-blue-600">
                                                {{ $voucher->points }} pts
                                            </td>
                                            <td class="p-2 md:border md:border-gray-100 block md:table-cell text-center">
                                                <form action="{{ route('staff.vouchers.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Delete this voucher?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700 font-bold text-xs underline">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-gray-400">No vouchers added yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>