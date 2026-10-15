<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')->orderByDesc('is_active')->orderBy('name')->paginate(20);

        return view('users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        return view('users.create', ['roles' => $this->assignableRoles($request->user())]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id' => $this->roleRules($request->user()),
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(Request $request, User $user): View
    {
        $this->ensureCanManage($request->user(), $user);

        return view('users.edit', [
            'user' => $user,
            'roles' => $this->assignableRoles($request->user()),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request->user(), $user);

        $isSelf = $request->user()->is($user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            // Your own role can't change, so it isn't validated against what you may assign.
            'role_id' => $isSelf ? ['nullable'] : $this->roleRules($request->user()),
            'is_active' => ['boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Admins can't demote or deactivate themselves and lock everyone out.
        if (! $isSelf) {
            $user->role_id = $validated['role_id'];
            $user->is_active = $request->boolean('is_active');
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Deactivate rather than delete, so the user's orders keep their history.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->ensureCanManage($request->user(), $user);

        if ($request->user()->is($user)) {
            return back()->with('error', "You can't deactivate your own account.");
        }

        $user->update(['is_active' => false]);

        return redirect()
            ->route('users.index')
            ->with('success', "{$user->name} has been deactivated and can no longer log in.");
    }

    /**
     * Only admins may hand out the Admin role, so staff managers can't promote
     * anyone (or themselves, through a colleague) to full access.
     */
    private function assignableRoles(User $actor)
    {
        return Role::orderBy('name')
            ->when(! $actor->isAdmin(), fn ($q) => $q->where('is_system', false))
            ->get();
    }

    private function roleRules(User $actor): array
    {
        return [
            'required',
            Rule::exists('roles', 'id')->when(! $actor->isAdmin(), fn ($rule) => $rule->where('is_system', 0)),
        ];
    }

    /**
     * Admin accounts can only be changed by admins, otherwise a staff manager
     * could reset an admin's password and sign in as them.
     */
    private function ensureCanManage(User $actor, User $user): void
    {
        abort_if($user->isAdmin() && ! $actor->isAdmin(), 403);
    }
}
