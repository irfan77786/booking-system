<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    


    public function up(): void
    {
        if (! Schema::hasTable('return_service')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $idExtra = DB::selectOne(
            'SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['return_service', 'id']
        );
        if ($idExtra && stripos((string) $idExtra->EXTRA, 'auto_increment') !== false) {
            return;
        }

         
        try {
            DB::statement('ALTER TABLE `return_service` MODIFY `created_at` TIMESTAMP NULL DEFAULT NULL');
        } catch (\Throwable $e) {
             
        }
        try {
            DB::statement('ALTER TABLE `return_service` MODIFY `updated_at` TIMESTAMP NULL DEFAULT NULL');
        } catch (\Throwable $e) {
        }

        $pk = DB::select('SHOW KEYS FROM `return_service` WHERE Key_name = ?', ['PRIMARY']);
        if (empty($pk)) {
            DB::statement('ALTER TABLE `return_service` ADD PRIMARY KEY (`id`)');
        }

        DB::statement('ALTER TABLE `return_service` MODIFY COLUMN `id` BIGINT NOT NULL AUTO_INCREMENT');
    }

    public function down(): void
    {
         
    }
};
