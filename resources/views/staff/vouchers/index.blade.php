<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Vouchers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="md:col-span-1">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">
                            ➕ Add New Voucher
                        </h3>

                        <form action="{{ route('staff.vouchers.store') }}" method="POST">
                            @csrf

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Voucher Name
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                    placeholder="e.g. Free Coffee"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Category
                                </label>
                                <select
                                    name="category"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="Food & Beverage">Food & Beverage</option>
                                    <option value="Travel">Travel</option>
                                    <option value="Shopping">Shopping</option>
                                    <option value="Service">Service</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Points Cost
                                </label>
                                <input
                                    type="number"
                                    name="points"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                    placeholder="e.g. 500"
                                >
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    rows="3"
                                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                    required
                                    placeholder="Terms and conditions..."
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="w-full rounded bg-blue-600 py-2 font-bold text-white transition hover:bg-blue-700"
                            >
                                Create Voucher
                            </button>
                        </form>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="mb-4 text-lg font-bold text-gray-800">
                            🎟️ Active Vouchers
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="block min-w-full border-collapse md:table">
                                <thead class="block md:table-header-group">
                                    <tr class="absolute -left-full -top-full block border border-gray-200 md:relative md:left-auto md:top-auto md:table-row">
                                        <th class="block bg-gray-100 p-2 text-left font-bold text-gray-600 md:table-cell">
                                            Name
                                        </th>
                                        <th class="block bg-gray-100 p-2 text-left font-bold text-gray-600 md:table-cell">
                                            Category
                                        </th>
                                        <th class="block bg-gray-100 p-2 text-left font-bold text-gray-600 md:table-cell">
                                            Points
                                        </th>
                                        <th class="block bg-gray-100 p-2 text-center font-bold text-gray-600 md:table-cell">
                                            Action
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="block md:table-row-group">
                                    @forelse ($vouchers as $voucher)
                                        <tr class="block border border-gray-200 bg-white hover:bg-gray-50 md:table-row md:border-none">
                                            <td class="block p-2 md:table-cell md:border md:border-gray-100">
                                                <span class="font-bold text-gray-800">
                                                    {{ $voucher->name }}
                                                </span>
                                            </td>

                                            <td class="block p-2 md:table-cell md:border md:border-gray-100">
                                                <span class="rounded bg-blue-100 px-2 py-1 text-xs text-blue-800">
                                                    {{ $voucher->category }}
                                                </span>
                                            </td>

                                            <td class="block p-2 font-bold text-blue-600 md:table-cell md:border md:border-gray-100">
                                                {{ $voucher->points }} pts
                                            </td>

                                            <td class="block p-2 text-center md:table-cell md:border md:border-gray-100">
                                                <form
                                                    action="{{ route('staff.vouchers.destroy', $voucher->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete this voucher?');"
                                                >
                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="text-xs font-bold text-red-500 underline hover:text-red-700"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="p-4 text-center text-gray-400">
                                                No vouchers added yet.
                                            </td>
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