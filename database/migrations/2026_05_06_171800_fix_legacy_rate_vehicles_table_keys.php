<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rate_vehicles')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $idColumn = collect(DB::select('SHOW FULL COLUMNS FROM rate_vehicles'))
            ->firstWhere('Field', 'id');

        if (! $idColumn) {
            return;
        }

        $hasPrimary = ($idColumn->Key ?? '') === 'PRI';
        $hasAutoIncrement = stripos((string) ($idColumn->Extra ?? ''), 'auto_increment') !== false;

        if (! $hasPrimary) {
            DB::statement(
                'ALTER TABLE `rate_vehicles` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)'
            );
        } elseif (! $hasAutoIncrement) {
            DB::statement(
                'ALTER TABLE `rate_vehicles` MODIFY `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT'
            );
        }

        $indexNames = collect(DB::select('SHOW INDEX FROM rate_vehicles'))
            ->pluck('Key_name')
            ->unique();

        if (! $indexNames->contains('rate_vehicles_vehicle_id_unique')) {
            Schema::table('rate_vehicles', static function (Blueprint $table) {
                $table->unique('vehicle_id', 'rate_vehicles_vehicle_id_unique');
            });
        }
    }

    public function down(): void
    {
        //
    }
};
