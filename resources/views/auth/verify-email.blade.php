<x-guest-layout>
    <div class="w-full">
        <div class="mb-6 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-100">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8m-16 9h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2z" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-800">
                Verify Your Email
            </h1>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Thanks for signing up. Before getting started, please verify your email address by clicking the link we just emailed to you.
                If you did not receive the email, you can request another one below.
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                A new verification link has been sent to your email address.
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="space-y-4">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Resend Verification Email
                    </button>
                </form>

                <div class="border-t pt-4 text-center">
                    <p class="mb-3 text-xs uppercase tracking-wider text-gray-400">
                        Need to switch account?
                    </p>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="text-sm font-semibold text-gray-600 transition hover:text-gray-900"
                        >
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>