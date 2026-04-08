<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('group_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->integer('cycle_number');
            $table->decimal('amount', 12, 2);
            $table->string('proof_path')->nullable();
            $table->text('note')->nullable();
            $table->date('due_date');
            $table->enum('status', ['pending', 'under_review', 'verified', 'rejected'])->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['group_id', 'user_id', 'cycle_number']);
            $table->index(['group_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contributions');
    }
};
