<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('We have sent a code to your email. Please enter it below along with your new password.') }}
    </div>

    <form method="POST" action="{{ route('password.update.otp') }}">
        @csrf

        <input type="hidden" name="email" value="{{ request('email') }}">

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700">Enter 6-Digit Code</label>
            <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm text-center text-2xl tracking-widest" type="text" name="code" required />
            @error('code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700">New Password</label>
            <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="password" name="password" required />
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="mt-4">
            <label class="block font-medium text-sm text-gray-700">Confirm Password</label>
            <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="password" name="password_confirmation" required />
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="ml-3 bg-green-600 text-white px-4 py-2 rounded-md font-bold uppercase hover:bg-green-700">
                {{ __('Reset Password') }}
            </button>
        </div>
    </form>
</x-guest-layout>