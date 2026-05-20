<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rate_vehicle_cities')) {
            return;
        }

        Schema::create('rate_vehicle_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->string('city_place_id', 128);
            $table->string('city_name', 255);
            $table->json('distance_rates')->nullable();
            $table->json('waiting_time_rates')->nullable();
            $table->decimal('gratuity', 8, 2)->nullable();
            $table->decimal('base_rate', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['vehicle_id', 'city_place_id']);
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_vehicle_cities');
    }
};
