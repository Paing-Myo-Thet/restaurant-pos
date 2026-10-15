<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The roles that used to be hard-coded, with the same access they had.
     * The system role (Admin) has every permission, so none are listed.
     */
    private const DEFAULT_ROLES = [
        'admin' => ['name' => 'Admin', 'is_system' => true, 'permissions' => []],
        'cashier' => ['name' => 'Cashier', 'is_system' => false, 'permissions' => [
            'dashboard.view', 'orders.view', 'orders.manage', 'orders.cancel', 'orders.checkout',
        ]],
        'waiter' => ['name' => 'Waiter', 'is_system' => false, 'permissions' => [
            'dashboard.view', 'orders.view', 'orders.manage', 'orders.print_draft', 'tables.manage',
        ]],
        'kitchen' => ['name' => 'Kitchen', 'is_system' => false, 'permissions' => [
            'orders.view', 'kitchen.manage',
        ]],
    ];

    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->json('permissions');
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')
                ->constrained('roles')->restrictOnDelete();
        });

        foreach (self::DEFAULT_ROLES as $oldRole => $role) {
            $id = DB::table('roles')->insertGetId([
                'name' => $role['name'],
                'permissions' => json_encode($role['permissions']),
                'is_system' => $role['is_system'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('users')->where('role', $oldRole)->update(['role_id' => $id]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('waiter')->after('password');
        });

        foreach (DB::table('roles')->get() as $role) {
            $oldRole = strtolower($role->name);

            DB::table('users')->where('role_id', $role->id)->update([
                'role' => array_key_exists($oldRole, self::DEFAULT_ROLES) ? $oldRole : 'waiter',
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
