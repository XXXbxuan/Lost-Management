<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- 🌟 DYNAMIC STATUS CARD --}}
            @if(Auth::user()->role === 'Passenger')
                {{-- PASSENGER VIEW: Show Points --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Passenger Status</h3>
                            <p class="text-sm text-gray-600">Your current membership details.</p>
                        </div>
                        <div class="flex space-x-8 text-center">
                            <div>
                                <span class="block text-2xl font-bold text-blue-600">{{ Auth::user()->points }}</span>
                                <span class="text-xs text-gray-500 uppercase">Reward Points</span>
                            </div>
                            <div>
                                <span class="block text-2xl font-bold text-gray-800">{{ ucfirst(Auth::user()->role) }}</span>
                                <span class="text-xs text-gray-500 uppercase">Account Type</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- ADMIN & STAFF VIEW: Show System Role --}}
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg border-l-4 border-indigo-600">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Official Staff Profile</h3>
                            <p class="text-sm text-gray-600">Airport Internal Management System Access.</p>
                        </div>
                        <div class="flex space-x-8 text-center">
                            <div>
                                <span class="block text-2xl font-bold text-indigo-600">{{ ucfirst(Auth::user()->role) }}</span>
                                <span class="text-xs text-gray-500 uppercase">Access Level</span>
                            </div>
                            <div>
                                <span class="block text-sm font-bold text-green-600 px-2 py-1 bg-green-50 rounded">ACTIVE</span>
                                <span class="text-xs text-gray-500 uppercase">Account Status</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- COMMON FORMS (Works for all roles) --}}
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- 🚨 DELETE PROTECTION: Usually, we don't let Staff/Admins delete themselves via the UI --}}
            @if(Auth::user()->role === 'Passenger')
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>