@php $checked = old('permissions', $role->permissions ?? []); @endphp

<div class="space-y-6">
    <div>
        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $role->name) }}" required maxlength="50"
               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
        @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Description <span class="text-gray-400">(optional)</span></label>
        <input id="description" type="text" name="description" value="{{ old('description', $role->description) }}" maxlength="255"
               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <fieldset class="space-y-5">
        <legend class="text-sm font-medium text-gray-700">Permissions</legend>

        @foreach (\App\Enums\Permission::grouped() as $group => $permissions)
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $group }}</p>

                <div class="space-y-2">
                    @foreach ($permissions as $permission)
                        <label class="flex items-start gap-3">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->value }}"
                                   @checked(in_array($permission->value, $checked, true))
                                   class="mt-0.5 rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">{{ $permission->label() }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach

        @error('permissions.*')
            <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </fieldset>
</div>
