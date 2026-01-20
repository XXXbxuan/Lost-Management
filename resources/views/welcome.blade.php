<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Airport Management System</title>
        
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-100 min-h-screen flex flex-col items-center justify-center">
        
        <div class="max-w-xl w-full bg-white p-8 rounded-lg shadow-lg text-center">
            <div class="mb-6 flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-20 h-20 text-blue-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Airport Management System</h1>
            <p class="text-gray-500 mb-8">Secure Admin Portal & Staff Operations</p>

            <div class="space-y-4">
                @if (Route::has('login'))
                    <div class="flex flex-col gap-4 justify-center items-center">
                        @auth
                            <div class="text-sm text-gray-600 mb-2">Welcome back, <strong>{{ Auth::user()->name }}</strong>!</div>
                            
                            <a href="{{ url('/dashboard') }}" class="w-full sm:w-2/3 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md flex items-center justify-center gap-2">
                                <span>Go to Dashboard</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="w-full sm:w-2/3 bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-md">
                                Log in to System
                            </a>

                            @endauth
                    </div>
                @endif
            </div>
            
            <div class="mt-8 text-xs text-gray-400 border-t pt-4">
                Authorized Personnel Only • Secure Connection
            </div>
        </div>

    </body>
</html>