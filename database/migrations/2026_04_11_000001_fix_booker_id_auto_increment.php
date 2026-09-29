<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    


    public function up(): void
    {
        if (! Schema::hasTable('booker')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        $idExtra = DB::selectOne(
            'SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['booker', 'id']
        );
        if ($idExtra && stripos((string) $idExtra->EXTRA, 'auto_increment') !== false) {
            return;
        }

        $otherAuto = DB::select(
            "SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'booker'
             AND COLUMN_NAME <> 'id' AND EXTRA LIKE '%auto_increment%'"
        );
        foreach ($otherAuto as $c) {
            $null = $c->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';
            $def = '';
            if ($c->COLUMN_DEFAULT !== null) {
                $default = $c->COLUMN_DEFAULT;
                if (in_array(strtoupper((string) $default), ['CURRENT_TIMESTAMP', 'CURRENT_TIMESTAMP()'], true)) {
                    $def = ' DEFAULT CURRENT_TIMESTAMP';
                } else {
                    $def = is_numeric($default)
                        ? " DEFAULT {$default}"
                        : ' DEFAULT '.DB::getPdo()->quote($default);
                }
            }
            DB::statement("ALTER TABLE `booker` MODIFY COLUMN `{$c->COLUMN_NAME}` {$c->COLUMN_TYPE} {$null}{$def}");
        }

        foreach (['created_at', 'updated_at'] as $col) {
            try {
                DB::statement("ALTER TABLE `booker` MODIFY `{$col}` TIMESTAMP NULL DEFAULT NULL");
            } catch (\Throwable $e) {
                 
            }
        }

        $pk = DB::select('SHOW KEYS FROM `booker` WHERE Key_name = ?', ['PRIMARY']);
        if (empty($pk)) {
            DB::statement('ALTER TABLE `booker` ADD PRIMARY KEY (`id`)');
        }

        $typeRow = DB::selectOne(
            'SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['booker', 'id']
        );
        $colType = $typeRow && $typeRow->COLUMN_TYPE ? (string) $typeRow->COLUMN_TYPE : 'bigint(20) unsigned';

        DB::statement("ALTER TABLE `booker` MODIFY COLUMN `id` {$colType} NOT NULL AUTO_INCREMENT");
    }

    public function down(): void
    {
         
    }
};
