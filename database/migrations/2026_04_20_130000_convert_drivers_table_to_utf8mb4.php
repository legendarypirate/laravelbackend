<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertDriversTableToUtf8mb4 extends Migration
{
    /**
     * Fix: drivers table was latin1 — Mongolian text (e.g. gender) caused
     * "Conversion from collation utf8mb4_unicode_ci into latin1_swedish_ci impossible".
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('drivers')) {
            return;
        }

        DB::statement('ALTER TABLE `drivers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse is not applied — do not revert charset on production data.
     *
     * @return void
     */
    public function down()
    {
    }
}
