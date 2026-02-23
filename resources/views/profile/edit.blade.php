<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
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

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>