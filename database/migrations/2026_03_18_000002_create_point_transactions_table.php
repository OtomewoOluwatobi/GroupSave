<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // daily_login, login_streak_bonus, pay_on_time, pay_early, referral, invite_accepted, cycle_completed, profile_completed, identity_verified, trust_review, redemption_extra_group_slot
            $table->integer('points'); // positive = earned, negative = spent
            $table->string('description');
            $table->json('metadata')->nullable(); // group name, referral user name, etc.
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
