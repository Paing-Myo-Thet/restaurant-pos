<x-app-layout>

    <div class="space-y-6">

        @if(session('dl-success'))

        <div class="bg-red-400 text-white text-bold rounded mt-2 p-3">
            {{session('dl-success')}}
        </div>

        @endif

        @if(session('success'))

        <div class="bg-green-400 text-white text-bold rounded mt-2 p-3">
            {{session('success')}}
        </div>

        @endif
        {{-- Page Header --}}
        <div class="flex items-center px-2 justify-between">
            <div>
                <h1 class="text-2xl font-bold">
                    Users
                </h1>

                <p class="text-gray-500">
                    Manage restaurant staff and their roles.
                </p>
            </div>

            <a href="{{ route('users.create') }}" class="rounded-lg bg-gray-900 px-4 py-2 text-white hover:bg-gray-700">
                + Add User
            </a>
        </div>

        {{-- Users Table --}}
        <div class="overflow-hidden rounded-lg bg-white shadow">

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead>
                        <tr class="border-b bg-gray-50 text-left">
                            <th class="px-6 py-3 text-sm font-semibold">
                                Name
                            </th>

                            <th class="px-6 py-3 text-sm font-semibold">
                                Email
                            </th>

                            <th class="px-6 py-3 text-sm font-semibold">
                                Role
                            </th>
                            <th class="px-6 py-3 text-sm font-semibold">
                                Action
                            </th>
                            <th class="px-6 py-3 text-sm font-semibold">
                                Created
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                        <tr class="border-b">

                            <td class="px-6 py-4">
                                {{ $user->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $user->role->value ?? $user->role }}
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{route('users.edit',$user)}}" class="hover:underline text-blue-300 text-md">
                                    edti
                                </a>
                                <form action="{{route('users.destroy',$user)}}" method="post" onclick="return confirm('Are you sure to delete this user')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="hover:underline text-red-300 text-md">delete</button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                {{ $user->created_at->format('Y-m-d') }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No users found.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
