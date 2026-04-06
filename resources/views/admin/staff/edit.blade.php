<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manage Staff') }}: {{ $staff->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto space-y-6 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                        Edit Details
                    </h3>

                    <form
                        method="POST"
                        action="{{ route('admin.staff.update', $staff->staff_id) }}"
                        onsubmit="return confirm('Are you sure you want to change the status of this user?');"
                    >
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <x-input-label for="name" :value="__('Full Name')" />
                                <x-text-input
                                    id="name"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="name"
                                    :value="old('name', $staff->name)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input
                                    id="email"
                                    class="block mt-1 w-full"
                                    type="email"
                                    name="email"
                                    :value="old('email', $staff->user->email)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="username" :value="__('Username')" />
                                <x-text-input
                                    id="username"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="username"
                                    :value="old('username', $staff->user->username)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('username')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="contact_number" :value="__('Contact Number')" />
                                <x-text-input
                                    id="contact_number"
                                    class="block mt-1 w-full"
                                    type="text"
                                    name="contact_number"
                                    :value="old('contact_number', $staff->contact_number)"
                                    required
                                />
                                <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <select
                                    id="role"
                                    name="role"
                                    class="block mt-1 w-full rounded-md border-gray-300 shadow-sm"
                                >
                                    <option value="Staff" {{ old('role', $staff->user->role) == 'Staff' ? 'selected' : '' }}>
                                        Staff
                                    </option>
                                    <option value="Admin" {{ old('role', $staff->user->role) == 'Admin' ? 'selected' : '' }}>
                                        Admin
                                    </option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <div class="col-span-1 md:col-span-2">
                                <x-input-label for="password" :value="__('New Password (Leave blank to keep current)')" />
                                <x-text-input
                                    id="password"
                                    class="block mt-1 w-full"
                                    type="password"
                                    name="password"
                                    autocomplete="new-password"
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <x-primary-button>
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-red-800 border-b border-red-200 pb-2 mb-4">
                        Account Actions
                    </h3>

                    <div class="flex justify-between items-center mb-6">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">
                                Account Status:
                                <span class="{{ $staff->status == 'Active' ? 'text-green-600' : 'text-red-600' }} font-bold">
                                    {{ $staff->status }}
                                </span>
                            </h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Blocked users cannot log in to the system.
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.staff.update', $staff->staff_id) }}"
                            onsubmit="return confirm('Are you sure you want to change the status of this user?');"
                        >
                            @csrf
                            @method('PUT')

                            <input type="hidden" name="toggle_status" value="1">

                            @if ($staff->status === 'Active')
                                <button
                                    type="submit"
                                    class="border border-red-600 text-red-600 bg-white px-4 py-2 rounded hover:bg-red-50 transition font-bold"
                                >
                                    Block User
                                </button>
                            @else
                                <button
                                    type="submit"
                                    class="border border-green-600 text-green-600 bg-white px-4 py-2 rounded hover:bg-green-50 transition font-bold"
                                >
                                    Unblock User
                                </button>
                            @endif
                        </form>
                    </div>

                    <div class="border-t border-red-200 my-4"></div>

                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <h4 class="font-bold text-red-700">Delete Account</h4>
                            <p class="text-sm text-red-600 mt-1">
                                Permanently remove this user and all associated data.
                            </p>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('admin.staff.destroy', $staff->staff_id) }}"
                            onsubmit="return confirm('Are you sure? This action cannot be undone.');"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                            >
                                Delete Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>