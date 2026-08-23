<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update Growth plan (monthly) with Price ID
        DB::table('plans')
            ->where('slug', 'growth')
            ->update(['stripe_price_id' => 'price_1Tq3XAGVRxfohcvD1bnz99ni']);

        // Update Enterprise plan (yearly) with Price ID
        DB::table('plans')
            ->where('slug', 'enterprise')
            ->update(['stripe_price_id' => 'price_1Tq3Z1GVRxfohcvDFcz25uCK']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NULL if rolling back
        DB::table('plans')
            ->whereIn('slug', ['growth', 'enterprise'])
            ->update(['stripe_price_id' => null]);
    }
};
