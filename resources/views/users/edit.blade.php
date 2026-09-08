```blade
<x-app-layout>

    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Edit User
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Update user information and permissions.
                </p>
            </div>

            <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Back to Users
            </a>
        </div>


        {{-- Edit Form Card --}}
        <div class="rounded-xl bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">

                @csrf
                @method('PUT')


                {{-- Name --}}
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">
                        Full Name
                    </label>

                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">

                    @error('name')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Email --}}
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">
                        Email Address
                    </label>

                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">

                    @error('email')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Role --}}
                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-gray-700">
                        User Role
                    </label>

                    <select id="role" name="role" required class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">

                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>

                        <option value="manager" {{ old('role', $user->role) === 'manager' ? 'selected' : '' }}>
                            Manager
                        </option>

                        <option value="waiter" {{ old('role', $user->role) === 'waiter' ? 'selected' : '' }}>
                            Waiter
                        </option>

                        <option value="kitchen" {{ old('role', $user->role) === 'kitchen' ? 'selected' : '' }}>
                            Kitchen
                        </option>

                        <option value="cashier" {{ old('role', $user->role) === 'cashier' ? 'selected' : '' }}>
                            Cashier
                        </option>

                    </select>

                    @error('role')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Password --}}
                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">
                        New Password
                    </label>

                    <input id="password" type="password" name="password" placeholder="Leave blank to keep current password" class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-200">

                    <p class="mt-1 text-xs text-gray-500">
                        Leave this field empty if you don't want to change the password.
                    </p>

                    @error('password')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>

                    <label for="role" class="block text-sm font-medium text-gray-700">
                        Role
                    </label>

                    <select name="role" id="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" {{ old('role', 'admin') === $role->value ? 'selected' : '' }}>
                            {{ ucfirst($role->value) }}
                        </option>
                        @endforeach
                    </select>

                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>


                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">

                    <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Update User
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
```
