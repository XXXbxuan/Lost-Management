<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            Edit Lost Report
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @include('staff.lost_reports.partials.form', [
                        'mode' => 'edit',
                        'lostItem' => $lostItem
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>