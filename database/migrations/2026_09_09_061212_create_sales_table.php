<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('sales', function (Blueprint $table) {
        $table->id();

        $table->foreignId('customer_id')
              ->nullable()
              ->constrained('customers')
              ->nullOnDelete();

        $table->foreignId('table_id')
              ->nullable()
              ->constrained('tables')
              ->nullOnDelete();

        $table->foreignId('user_id')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->decimal('sub_total', 10, 2);
        $table->decimal('descount', 10, 2)->default(0);
        $table->decimal('total', 10, 2);

        $table->enum('status', [
            'pending',
            'sent',
            'preparing',
            'ready',
            'served',
            'cancelled',
        ])->default('pending');

        $table->enum('payment_status', [
            'paid',
            'unpaid',
        ])->default('unpaid');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
