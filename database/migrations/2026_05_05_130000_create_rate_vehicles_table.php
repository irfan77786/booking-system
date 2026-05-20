<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rate_vehicles')) {
            return;
        }

        Schema::create('rate_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id')->unique();
            $table->json('distance_rates')->nullable();
            $table->json('waiting_time_rates')->nullable();
            $table->decimal('gratuity', 8, 2)->default(0);
            $table->decimal('base_rate', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rate_vehicles');
    }
};
