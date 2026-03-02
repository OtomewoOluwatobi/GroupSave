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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_id', 20)->unique(); // TK-001 format
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->enum('category', [
                'account',      // Account & Security
                'groups',       // Savings Groups
                'payments',     // Payments & Contributions
                'payouts',      // Withdrawals & Payouts
                'notifications',// Notifications
                'billing',      // Subscription & Billing
                'technical',    // Technical Issues
                'fraud',        // Fraud & Safety
            ]);
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', [
                'open',
                'in_review',
                'awaiting',     // Awaiting user response
                'escalated',
                'resolved',
                'closed',
            ])->default('open');
            $table->text('message');
            $table->string('assigned_to')->nullable(); // Support agent email/name
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
