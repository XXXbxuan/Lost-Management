<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a 6-digit code to choose a new one.') }}
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <label class="block font-medium text-sm text-gray-700">Email</label>
            <input class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" type="email" name="email" required autofocus />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="ml-3 bg-black text-white px-4 py-2 rounded-md font-bold uppercase">
                {{ __('Send Code') }}
            </button>
        </div>
    </form>
</x-guest-layout>