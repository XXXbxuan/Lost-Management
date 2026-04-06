<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight text-red-600">
            {{ __('🚨 Report Lost Item (Passenger)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @include('staff.lost_reports.partials.form', [
                        'mode' => 'create',
                        'lostItem' => null,
                    ])
                </div>
            </div>
        </div>
    </div>
</x-app-layout>