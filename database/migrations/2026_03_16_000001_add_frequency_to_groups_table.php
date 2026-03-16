<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->enum('contribution_frequency', ['daily', 'weekly', 'monthly'])
                  ->default('monthly')
                  ->after('payment_out_day');

            // Day of week (0=Sun … 6=Sat) — used only when frequency = 'weekly'
            $table->tinyInteger('payment_out_weekday')
                  ->nullable()
                  ->after('contribution_frequency');
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            $table->dropColumn(['contribution_frequency', 'payment_out_weekday']);
        });
    }
};
