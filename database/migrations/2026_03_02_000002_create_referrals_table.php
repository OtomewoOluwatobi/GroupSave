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
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('referrer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('referred_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('points_awarded')->default(0);
            $table->enum('status', ['pending', 'active', 'expired'])->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();

            // Prevent duplicate referrals
            $table->unique(['referrer_id', 'referred_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
