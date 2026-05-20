<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        // 1) Add the columns the application code expects.
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('id');
            }

            if (! Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });

        // 2) Backfill first_name / last_name from the legacy `name` column
        //    so existing accounts keep working in the UI.
        if (Schema::hasColumn('users', 'name')) {
            DB::table('users')
                ->whereNull('first_name')
                ->whereNotNull('name')
                ->orderBy('id')
                ->chunkById(500, function ($users) {
                    foreach ($users as $user) {
                        $parts = preg_split('/\s+/', trim((string) $user->name), 2);
                        $first = $parts[0] ?? null;
                        $last  = $parts[1] ?? null;

                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'first_name' => $first,
                                'last_name'  => $last,
                            ]);
                    }
                });

            // 3) Make `name` nullable so future inserts (which only set
            //    first_name / last_name) succeed under MySQL strict mode.
            Schema::table('users', function (Blueprint $table) {
                $table->string('name')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            foreach (['first_name', 'last_name', 'phone'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
