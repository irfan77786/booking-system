<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rate_city_groups')) {
            return;
        }

        Schema::create('rate_city_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('cities');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_city_groups');
    }
};
