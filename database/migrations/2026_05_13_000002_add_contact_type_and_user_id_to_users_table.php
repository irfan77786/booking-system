<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    

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
