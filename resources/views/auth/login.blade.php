<x-guest-layout>
    <div class="mb-6 flex justify-center p-1 bg-gray-100 rounded-lg">
        <button type="button" onclick="setRole('Passenger')" id="btn-passenger" 
                class="flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all">
            Passenger
        </button>
        <button type="button" onclick="setRole('Staff')" id="btn-staff" 
                class="flex-1 py-2 rounded-md text-gray-500 transition-all">
            Staff / Admin
        </button>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <input type="hidden" name="login_role" id="login_role" value="Passenger">

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <div id="register-link-container">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                    {{ __('New Passenger? Register here') }}
                </a>
            </div>

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        {{-- ========================================== --}}
        {{--       GOOGLE LOGIN BUTTON SECTION          --}}
        {{-- ========================================== --}}
        <div class="mt-6" id="google-login-container">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">Or continue with</span>
                </div>
            </div>

            <div class="mt-6">
                <a href="{{ route('google.login') }}" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 16.133 0 12.48 0 5.867 0 .533 5.333.533 12S5.867 24 12.48 24c3.44 0 6.333-1.133 8.373-3.2 2.12-2.12 2.68-5.387 2.68-8H12.48z"/>
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
        {{-- ========================================== --}}

    </form>

    <script>
        function setRole(role) {
            document.getElementById('login_role').value = role;
            const isPassenger = (role === 'Passenger');
            
            // 1. Change Button Styles
            document.getElementById('btn-passenger').className = isPassenger 
                ? 'flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all' 
                : 'flex-1 py-2 rounded-md text-gray-500 transition-all';
            
            document.getElementById('btn-staff').className = !isPassenger 
                ? 'flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all' 
                : 'flex-1 py-2 rounded-md text-gray-500 transition-all';
            
            // 2. Hide "Register" link for Staff
            document.getElementById('register-link-container').style.display = isPassenger ? 'block' : 'none';

            // 3. Hide "Google Login" for Staff (NEW ADDITION)
            const googleContainer = document.getElementById('google-login-container');
            if (googleContainer) {
                googleContainer.style.display = isPassenger ? 'block' : 'none';
            }
        }
    </script>
</x-guest-layout>