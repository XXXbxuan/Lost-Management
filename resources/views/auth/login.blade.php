<x-guest-layout>
    <div class="mb-6 flex justify-center p-1 bg-gray-100 rounded-lg">
        <button type="button" onclick="setRole('Passenger')" id="btn-passenger" 
                class="flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all">
            Passenger (乘客)
        </button>
        <button type="button" onclick="setRole('Staff')" id="btn-staff" 
                class="flex-1 py-2 rounded-md text-gray-500 transition-all">
            Staff / Admin (员工)
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
    </form>

    <script>
        function setRole(role) {
            document.getElementById('login_role').value = role;
            const isPassenger = (role === 'Passenger');
            
            // 切换按钮视觉样式
            document.getElementById('btn-passenger').className = isPassenger 
                ? 'flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all' 
                : 'flex-1 py-2 rounded-md text-gray-500 transition-all';
            
            document.getElementById('btn-staff').className = !isPassenger 
                ? 'flex-1 py-2 rounded-md bg-white shadow-sm text-blue-600 font-bold transition-all' 
                : 'flex-1 py-2 rounded-md text-gray-500 transition-all';
            
            // 如果是员工模式，隐藏注册链接（因为员工账号由 Admin 创建 ）
            document.getElementById('register-link-container').style.display = isPassenger ? 'block' : 'none';
        }
    </script>
</x-guest-layout>
