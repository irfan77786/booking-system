<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    


    public function up(): void
    {
        if (! Schema::hasTable('bookings')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $idExtra = DB::selectOne(
            'SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['bookings', 'id']
        );
        if ($idExtra && stripos((string) $idExtra->EXTRA, 'auto_increment') !== false) {
            return;
        }

        foreach (['created_at', 'updated_at'] as $col) {
            try {
                DB::statement("ALTER TABLE `bookings` MODIFY `{$col}` TIMESTAMP NULL DEFAULT NULL");
            } catch (\Throwable $e) {
                 
            }
        }

        $pk = DB::select('SHOW KEYS FROM `bookings` WHERE Key_name = ?', ['PRIMARY']);
        if (empty($pk)) {
            DB::statement('ALTER TABLE `bookings` ADD PRIMARY KEY (`id`)');
        }

        $typeRow = DB::selectOne(
            'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['bookings', 'id']
        );
        $colType = $typeRow && $typeRow->COLUMN_TYPE ? (string) $typeRow->COLUMN_TYPE : 'bigint(20) unsigned';

        DB::statement("ALTER TABLE `bookings` MODIFY COLUMN `id` {$colType} NOT NULL AUTO_INCREMENT");
    }

    public function down(): void
    {
         
    }
};
