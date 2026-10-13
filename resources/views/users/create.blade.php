<x-app-layout>

    <div class="space-y-6">

        <div>
            <h1 class="text-2xl font-bold">
                Add User
            </h1>

            <p class="text-gray-500">
                Create a new restaurant staff account.
            </p>
        </div>

        <div class="rounded-lg bg-white p-6 shadow">

            <form method="POST" action="{{route('users.store')}}">

                @csrf

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Name
                    </label>

                    <input type="text" name="name" value="{{old('name')}}" class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Enter name">

                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Email --}}
                <div class="mt-4">

                    <label class="block text-sm font-medium text-gray-700">
                        Email
                    </label>

                    <input type="email" name="email" value="{{old('email')}}" class="mt-1 block w-full rounded-lg
                     border-gray-300" placeholder="Enter email">

                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Password --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">
                        Password
                    </label>

                    <input type="password" name="password" value="{{old('password')}}" class="mt-1 block w-full rounded-lg
                     border-gray-300" placeholder="Enter password">

                    @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Confirmed Password--}}
                <div class="mt-4">

                    <label class="block text-sm font-medium text-gray-700">
                        Confirm Password
                    </label>

                    <input type="password" name="password_confirmation" class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Confirm password">

                    @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
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
                            {{ucfirst($role->value) }}
                        </option>
                        @endforeach
                    </select>

                    @error('role')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                </div>

                {{-- Buttons --}}
                <div class="mt-6 flex gap-3">

                    <a href="{{ route('users.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700">
                        Cancel
                    </a>

                    <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-white">
                        Create User
                    </button>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
