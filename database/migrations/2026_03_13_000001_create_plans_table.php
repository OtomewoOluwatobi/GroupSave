<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->unsignedInteger('price')->default(0); // stored in pence: 0, 499, 19900
            $table->string('currency', 3)->default('GBP');
            $table->enum('billing', ['free_forever', 'monthly', 'yearly']);
            $table->json('features');
            $table->json('built_for')->nullable();
            $table->unsignedInteger('max_groups')->default(1);
            $table->unsignedInteger('max_members_per_group')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
