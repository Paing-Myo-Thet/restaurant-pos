<x-app-layout>

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Staff</h1>
                <p class="text-sm text-gray-500">Accounts that can sign in to the POS</p>
            </div>

            <a href="{{ route('users.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + Add staff
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left font-semibold text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @forelse ($users as $user)
                        <tr class="{{ $user->is_active ? '' : 'bg-gray-50 text-gray-400' }}">
                            <td class="px-6 py-4 font-medium">
                                {{ $user->name }}
                                @if (auth()->user()->is($user))
                                    <span class="ml-1 text-xs text-gray-400">(you)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4 capitalize">{{ $user->role?->value }}</td>
                            <td class="px-6 py-4">
                                @if ($user->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">Active</span>
                                @else
                                    <span class="rounded-full bg-gray-200 px-3 py-1 text-xs font-semibold text-gray-600">Deactivated</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('users.edit', $user) }}" class="font-medium text-blue-600 hover:underline">Edit</a>

                                    @if ($user->is_active && ! auth()->user()->is($user))
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                              onsubmit="return confirm(@js("Deactivate {$user->name}? They will no longer be able to sign in."))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:underline">Deactivate</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No staff found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $users->links() }}

    </div>

</x-app-layout>
