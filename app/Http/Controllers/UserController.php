<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }
    
    public function create(): View
    {
        return view('users.create');

    }
   
       
    public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|confirmed|min:8',
        'role' => ['required', Rule::enum(UserRole::class)],
    ]);
    User::create($validated);

    return redirect()
        ->route('users.index')
        ->with('success', 'User created successfully.');
}

public function edit(User $user): View
{
    return view('users.edit',compact('user'));
}


    public function update(Request $request, User $user): RedirectResponse
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required',
            'email',
            'max:255',
            'unique:users,email,' . $user->id,
        ],
        'password' => ['nullable', 'string', 'min:8'],
        'role' => ['required', Rule::enum(UserRole::class)],
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];

    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }

    $user->save();

    return redirect()
        ->route('users.index')
        ->with('success', 'User updated successfully.');
}

public function destroy(User $user) {
    $user->delete();
    
    return redirect()
    ->route('users.index')
    ->with('success', 'User deleted successfully');
}
    
}

