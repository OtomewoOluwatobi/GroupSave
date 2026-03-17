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
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique(); // account, groups, payments, etc.
            $table->string('label');          // "Account & Security"
            $table->string('icon');           // Emoji or icon class
            $table->string('description');    // "Password, 2FA, login issues"
            $table->string('color')->nullable(); // Hex color for UI
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faq_categories');
    }
};
