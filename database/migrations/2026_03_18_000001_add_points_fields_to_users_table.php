<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)->after('referral_points');
            $table->unsignedInteger('login_streak')->default(0)->after('points');
            $table->date('last_login_date')->nullable()->after('login_streak');
            $table->unsignedInteger('extra_group_slots')->default(0)->after('last_login_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'login_streak', 'last_login_date', 'extra_group_slots']);
        });
    }
};
