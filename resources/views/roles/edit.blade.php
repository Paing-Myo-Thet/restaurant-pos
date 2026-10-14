<x-app-layout>

    <div class="mx-auto max-w-2xl space-y-6">

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Role</h1>
                <p class="mt-1 text-sm text-gray-500">Changes apply to everyone with this role straight away.</p>
            </div>

            <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Back to Roles
            </a>
        </div>

        <div class="rounded-xl bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('roles.update', $role) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @include('roles._form')

                <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('roles.index') }}" class="rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
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
