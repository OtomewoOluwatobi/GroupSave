<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            // 1-based slot position; NULL = not yet assigned
            $table->unsignedTinyInteger('payout_slot')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('payout_slot');
        });
    }
};
