<?php

namespace App\Http\Controllers;

use App\Enums\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::withCount('users')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        return view('roles.create', ['role' => new Role(['permissions' => []])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $role = Role::create($this->validateRole($request));

        return redirect()
            ->route('roles.index')
            ->with('success', "Role {$role->name} created.");
    }

    public function edit(Role $role): View
    {
        $this->ensureEditable($role);

        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $this->ensureEditable($role);

        $role->update($this->validateRole($request, $role));

        return redirect()
            ->route('roles.index')
            ->with('success', "Role {$role->name} updated.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->ensureEditable($role);

        if ($count = $role->users()->count()) {
            return redirect()
                ->route('roles.index')
                ->with('error', "{$role->name} is assigned to {$count} ".str('user')->plural($count).'. Move them to another role first.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', "Role {$role->name} deleted.");
    }

    private function validateRole(Request $request, ?Role $role = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('roles', 'name')->ignore($role)],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::enum(Permission::class)],
        ]);

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'permissions' => array_values(array_unique($validated['permissions'] ?? [])),
        ];
    }

    /**
     * The Admin role always has full access, so it can't be edited or deleted
     * and nobody can lock everyone out of staff and role management.
     */
    private function ensureEditable(Role $role): void
    {
        abort_if($role->is_system, 403, 'The Admin role always has every permission and cannot be changed.');
    }
}
