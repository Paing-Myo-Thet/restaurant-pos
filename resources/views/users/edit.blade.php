<x-app-layout>

    @php $isSelf = auth()->user()->is($user); @endphp

    <div class="mx-auto max-w-2xl space-y-6">

        {{-- Page Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Staff</h1>
                <p class="mt-1 text-sm text-gray-500">Update details, role and access.</p>
            </div>

            <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Back to Staff
            </a>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Role --}}
                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" name="role" required @disabled($isSelf)
                            class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm disabled:bg-gray-100">
                        @foreach (\App\Enums\UserRole::cases() as $role)
                            <option value="{{ $role->value }}" @selected(old('role', $user->role?->value) === $role->value)>
                                {{ ucfirst($role->value) }}
                            </option>
                        @endforeach
                    </select>
                    @if ($isSelf)
                        {{-- Disabled fields aren't submitted, so send the current role. --}}
                        <input type="hidden" name="role" value="{{ $user->role?->value }}">
                        <p class="mt-1 text-xs text-gray-500">You can't change your own role.</p>
                    @endif
                    @error('role')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Active --}}
                @unless ($isSelf)
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active))
                               class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700">Active — can sign in</span>
                    </label>
                @endunless

                {{-- Password --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-700">New Password</label>
                        <input id="password" type="password" name="password" autocomplete="new-password" placeholder="Leave blank to keep"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password"
                               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('users.index') }}" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-lg bg-blue-600 px-6 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>

        </div>

    </div>

</x-app-layout>
