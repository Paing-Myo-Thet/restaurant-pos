<x-app-layout>

    <div class="space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Roles</h1>
                <p class="text-sm text-gray-500">What each kind of staff account can do</p>
            </div>

            <a href="{{ route('roles.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                + Add role
            </a>
        </div>

        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left font-semibold text-gray-700">
                    <tr>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Permissions</th>
                        <th class="px-6 py-3">Staff</th>
                        <th class="px-6 py-3"></th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    @foreach ($roles as $role)
                        <tr>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $role->name }}</p>
                                @if ($role->description)
                                    <p class="text-xs text-gray-500">{{ $role->description }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($role->is_system)
                                    <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-semibold text-purple-800">Full access</span>
                                @else
                                    {{ count($role->permissions) }} of {{ count(\App\Enums\Permission::cases()) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $role->users_count }}</td>
                            <td class="px-6 py-4">
                                @unless ($role->is_system)
                                    <div class="flex items-center justify-end gap-3">
                                        <a href="{{ route('roles.edit', $role) }}" class="font-medium text-blue-600 hover:underline">Edit</a>

                                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                              data-confirm-title="Delete {{ $role->name }}?" data-confirm="Staff must be moved to another role first. This can't be undone." data-confirm-button="Delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-medium text-red-600 hover:underline">Delete</button>
                                        </form>
                                    </div>
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

</x-app-layout>
