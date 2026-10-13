<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('note')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('note');
            $table->decimal('amount_received', 10, 2)->nullable()->after('payment_method');
            $table->decimal('change_due', 10, 2)->nullable()->after('amount_received');
            $table->timestamp('sent_at')->nullable()->after('change_due');
            $table->timestamp('paid_at')->nullable()->after('sent_at');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            // Cost at the time of sale, so profit reports survive later cost changes.
            $table->decimal('unit_cost', 10, 2)->nullable()->after('unit_price');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('sale_id')->nullable()->after('user_id')
                ->constrained('sales')->nullOnDelete();
            $table->string('note')->nullable()->after('quantity');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sale_id');
            $table->dropColumn('note');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('unit_cost');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['note', 'payment_method', 'amount_received', 'change_due', 'sent_at', 'paid_at']);
        });
    }
};
