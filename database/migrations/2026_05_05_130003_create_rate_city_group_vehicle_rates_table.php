<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rate_city_group_vehicle_rates')) {
            return;
        }

        Schema::create('rate_city_group_vehicle_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->json('distance_rates')->nullable();
            $table->json('waiting_time_rates')->nullable();
            $table->decimal('gratuity', 8, 2)->default(0);
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('rate_city_groups')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
            $table->unique(['group_id', 'vehicle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_city_group_vehicle_rates');
    }
};
