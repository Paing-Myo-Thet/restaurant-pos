<x-app-layout>

    <div class="p-6">

        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold">
                Edit Customer
            </h1>

            <p class="text-gray-600">
                Update customer information
            </p>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-100 p-4 text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- Edit Form --}}
        <form method="POST" action="{{ route('customers.update', $customer) }}" class="max-w-2xl space-y-6 rounded-lg bg-white p-6 shadow">

            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="mb-2 block font-medium">
                    Name
                </label>

                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="w-full rounded-lg border-gray-300">

                @error('name')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="mb-2 block font-medium">
                    Phone
                </label>

                <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full rounded-lg border-gray-300">

                @error('phone')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="mb-2 block font-medium">
                    Email
                </label>

                <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="w-full rounded-lg border-gray-300">

                @error('email')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Address --}}
            <div>
                <label class="mb-2 block font-medium">
                    Address
                </label>

                <textarea name="address" rows="4" class="w-full rounded-lg border-gray-300">{{ old('address', $customer->address) }}</textarea>

                @error('address')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">

                <a href="{{ route('customers.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 hover:bg-gray-300">
                    Cancel
                </a>

                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    Update Customer
                </button>

            </div>

        </form>

    </div>

</x-app-layout>
