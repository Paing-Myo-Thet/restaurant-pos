<?php

use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->role('admin')->create();
    $this->adminRole = Role::where('is_system', true)->first();
});

function userWithPermissions(array $permissions): User
{
    $role = Role::create(['name' => 'Custom '.uniqid(), 'permissions' => $permissions]);

    return User::factory()->create(['role_id' => $role->id]);
}

test('the four default roles exist after migrating', function () {
    expect(Role::orderBy('name')->pluck('name')->all())->toBe(['Admin', 'Cashier', 'Kitchen', 'Waiter']);
});

test('admins can create a role and its staff get exactly its permissions', function () {
    $this->actingAs($this->admin)->post(route('roles.store'), [
        'name' => 'Supervisor',
        'description' => 'Floor lead',
        'permissions' => ['orders.view', 'reports.view'],
    ])->assertRedirect(route('roles.index'));

    $supervisor = User::factory()->create(['role_id' => Role::where('name', 'Supervisor')->value('id')]);

    $this->actingAs($supervisor)->get(route('reports.index'))->assertOk();
    $this->actingAs($supervisor)->get(route('sales.index'))->assertOk();
    $this->actingAs($supervisor)->get(route('users.index'))->assertForbidden();
    $this->actingAs($supervisor)->get(route('sales.create'))->assertForbidden();
});

test('changing a role changes what its staff can do straight away', function () {
    $user = userWithPermissions(['orders.view']);
    $this->actingAs($user)->get(route('reports.index'))->assertForbidden();

    $this->actingAs($this->admin)->put(route('roles.update', $user->role), [
        'name' => $user->role->name,
        'permissions' => ['orders.view', 'reports.view'],
    ])->assertRedirect(route('roles.index'));

    $this->actingAs($user->fresh())->get(route('reports.index'))->assertOk();
});

test('unknown permissions are rejected', function () {
    $this->actingAs($this->admin)->post(route('roles.store'), [
        'name' => 'Sneaky',
        'permissions' => ['everything'],
    ])->assertSessionHasErrors('permissions.0');

    expect(Role::where('name', 'Sneaky')->exists())->toBeFalse();
});

test('the admin role cannot be edited or deleted', function () {
    $this->actingAs($this->admin)->get(route('roles.edit', $this->adminRole))->assertForbidden();
    $this->actingAs($this->admin)->put(route('roles.update', $this->adminRole), ['name' => 'Admin', 'permissions' => []])->assertForbidden();
    $this->actingAs($this->admin)->delete(route('roles.destroy', $this->adminRole))->assertForbidden();

    expect($this->adminRole->fresh()->is_system)->toBeTrue();
});

test('a role still assigned to staff cannot be deleted', function () {
    $waiterRole = Role::where('name', 'Waiter')->first();
    User::factory()->role('waiter')->create();
    $unused = Role::create(['name' => 'Unused', 'permissions' => []]);

    $this->actingAs($this->admin)->delete(route('roles.destroy', $waiterRole))->assertSessionHas('error');
    $this->actingAs($this->admin)->delete(route('roles.destroy', $unused))->assertSessionHas('success');

    expect($waiterRole->fresh())->not->toBeNull()
        ->and($unused->fresh())->toBeNull();
});

test('staff without role management cannot open roles', function () {
    $this->actingAs(User::factory()->role('cashier')->create())->get(route('roles.index'))->assertForbidden();
    $this->actingAs($this->admin)->get(route('roles.index'))->assertOk()->assertSee('Full access');
    $this->actingAs($this->admin)->get(route('roles.create'))->assertOk()->assertSee('Take payment and print receipts');
    $this->actingAs($this->admin)->get(route('roles.edit', Role::where('name', 'Cashier')->first()))
        ->assertOk()
        ->assertSee('value="Cashier"', false);
});

test('staff managers cannot promote anyone to admin or change admin accounts', function () {
    $manager = userWithPermissions(['users.manage']);
    $waiter = User::factory()->role('waiter')->create();

    $this->actingAs($manager)->get(route('users.create'))->assertOk()->assertDontSee('>Admin<', false);

    $this->actingAs($manager)->post(route('users.store'), [
        'name' => 'New Boss', 'email' => 'boss@example.com',
        'password' => 'password123', 'password_confirmation' => 'password123',
        'role_id' => $this->adminRole->id,
    ])->assertSessionHasErrors('role_id');

    $this->actingAs($manager)->put(route('users.update', $waiter), [
        'name' => $waiter->name, 'email' => $waiter->email, 'role_id' => $this->adminRole->id, 'is_active' => 1,
    ])->assertSessionHasErrors('role_id');

    $this->actingAs($manager)->get(route('users.edit', $this->admin))->assertForbidden();
    $this->actingAs($manager)->put(route('users.update', $this->admin), [
        'name' => 'Hacked', 'email' => $this->admin->email, 'password' => 'newpassword1', 'password_confirmation' => 'newpassword1',
    ])->assertForbidden();

    expect($waiter->fresh()->isAdmin())->toBeFalse()
        ->and($this->admin->fresh()->name)->not->toBe('Hacked');
});

test('staff managers can still create staff with ordinary roles', function () {
    $manager = userWithPermissions(['users.manage']);

    $this->actingAs($manager)->post(route('users.store'), [
        'name' => 'New Waiter', 'email' => 'new@example.com',
        'password' => 'password123', 'password_confirmation' => 'password123',
        'role_id' => Role::where('name', 'Waiter')->value('id'),
    ])->assertRedirect(route('users.index'));

    expect(User::where('email', 'new@example.com')->first()->role->name)->toBe('Waiter');
});

test('staff without the dashboard land on a page they can use', function () {
    $this->actingAs(userWithPermissions(['orders.view']))
        ->get(route('dashboard'))->assertRedirect(route('sales.index'));

    $this->actingAs(userWithPermissions([]))
        ->get(route('dashboard'))->assertRedirect(route('profile.edit'));
});
