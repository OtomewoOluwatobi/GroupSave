<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email');
            $table->string('source')->nullable();
            $table->string('location')->nullable();
            $table->string('cooking_habit')->nullable();
            $table->string('grocery_frequency')->nullable();
            $table->text('pain_points')->nullable();
            $table->unsignedTinyInteger('hassle_score')->nullable();
            $table->unsignedTinyInteger('likelihood_score')->nullable();
            $table->string('fee_pref')->nullable();
            $table->string('delivery_pref')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
