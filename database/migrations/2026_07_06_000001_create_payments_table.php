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
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid()->primary(); // UUID primary key
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('plan_id')->nullable()->constrained()->onDelete('set null');
            $table->string('stripe_payment_intent_id')->nullable()->unique();
            $table->string('stripe_charge_id')->nullable()->unique();
            $table->string('stripe_invoice_id')->nullable();
            $table->integer('amount'); // in cents
            $table->string('currency')->default('USD');
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_type', ['subscription', 'one_time'])->default('subscription');
            $table->json('stripe_response')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('succeeded_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
