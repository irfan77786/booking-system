<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * The /users address-book page and the registration / booking flow
     * expect `contact_type` and `user_id` on the `users` table. These
     * columns existed in legacy `migrations-old/` files that never made
     * it into the active migrations folder, so production tables are
     * missing them and queries like
     *   select ... `contact_type` ... where `user_id` = ?
     * fail with SQLSTATE[42S22] 1054 Unknown column.
     */
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'contact_type')) {
                $table->string('contact_type')->default('passenger')->index();
            }

            if (! Schema::hasColumn('users', 'user_id')) {
                // No DB-level foreign key: legacy `users` tables may not
                // have `id` as a primary key MySQL can reference. We
                // index instead, which is what the queries actually need.
                $table->unsignedBigInteger('user_id')->nullable()->index();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'user_id')) {
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('users', 'contact_type')) {
                $table->dropColumn('contact_type');
            }
        });
    }
};
