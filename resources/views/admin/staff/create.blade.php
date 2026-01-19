<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add New Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6 border-b pb-4">
                        <h3 class="text-lg font-medium text-gray-900">Staff Registration</h3>
                        <p class="mt-1 text-sm text-gray-500">Create a new account for system access.</p>
                    </div>

                    <form method="POST" action="{{ route('admin.staff.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input id="contact_number" class="block mt-1 w-full" type="text" name="contact_number" :value="old('contact_number')" required placeholder="e.g. 012-3456789" />
                                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="department" :value="__('Department')" />
                                <select id="department" name="department" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="Terminal Operations">Terminal Operations</option>
                                    <option value="Customer Service">Customer Service</option>
                                    <option value="Security">Security</option>
                                    <option value="Management">Management</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                    <option value="Staff">Staff (Standard Access)</option>
                                    <option value="Admin">Admin (Full Control)</option>
                                </select>
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 border-t pt-4 gap-4">
                            <a href="{{ route('admin.staff.index') }}" class="text-sm text-gray-600 hover:text-gray-900 underline decoration-gray-400 underline-offset-4">
                                Cancel
                            </a>
                            <x-primary-button class="ml-3">
                                {{ __('Create Account') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>