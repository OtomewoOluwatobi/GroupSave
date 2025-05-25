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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->integer('total_users')->default(0);
            $table->decimal('target_amount', 10, 2);
            $table->decimal('payable_amount', 10, 2);
            $table->date('expected_start_date');
            $table->date('expected_end_date');
            $table->integer('payment_out_day')->default(1);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
