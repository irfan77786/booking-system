<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * PaymentMethodController persists the Stripe customer ID back to the
     * authenticated user, so the `users` table needs a `stripe_customer_id`
     * column. The legacy migration for this column lives in
     * database/migrations-old/ and was never run on production, causing
     * SQLSTATE[42S22] 1054 Unknown column 'stripe_customer_id' in 'SET'
     * on /payment-methods.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::hasColumn('users', 'stripe_customer_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            // No ->after() so this works regardless of which other columns
            // exist on legacy users tables.
            $table->string('stripe_customer_id')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! Schema::hasColumn('users', 'stripe_customer_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('stripe_customer_id');
        });
    }
};
