<x-guest-layout>

    <!-- Login Card -->
    <div class="rounded-2xl bg-white p-2">

        <div class="mx-auto mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-gray-900 text-2xl">
            🍽️
        </div>

        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
            Restaurant <span class="text-blue-600">POS</span>
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Welcome back! Sign in to continue.
        </p>
        <!-- Session Status -->
        <x-auth-session-status class="mb-5" :status="session('status')" />

        <!-- Validation Errors -->
        @if ($errors->any())
        <div class="mb-3 rounded-lg border border-red-200 bg-red-50 p-3">
            <ul class="list-inside list-disc space-y-1 text-sm text-red-600">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email address')" class="mb-1.5 text-sm font-semibold text-gray-700" />

                <x-text-input id="email" type="email" name="email" :value="old('email')" placeholder="you@example.com" required autofocus autocomplete="username" class="block w-full rounded-lg border-gray-300 px-3 mb-6 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />

                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>

                <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-gray-700" />


                <x-text-input id="password" type="password" name="password" placeholder="Enter your password" required autocomplete="current-password" class="block w-full rounded-lg border-gray-300 px-3 mb-6 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />

                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Remember Me -->
            <div class=" flex items-center justify-between ">
                <div class="flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                    <label for="remember_me" class="ms-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-medium text-blue-600 hover:text-blue-800 sm:text-sm">
                    Forgot password?
                </a>
                @endif
            </div>

            <!-- Login Button -->
            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none mt-5 focus:ring-4 focus:ring-blue-100">
                Sign in
            </button>
        </form>

        <!-- Register Link -->
        @if (Route::has('register'))
        <div class="mt-4 border-t border-gray-100 pt-4 text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800">
                    Register
                </a>
            </p>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <p class="mt-3 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Restaurant POS
    </p>

</x-guest-layout>
