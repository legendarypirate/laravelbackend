<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (! Schema::hasColumn('drivers', 'gender')) {
                $table->string('gender', 32)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasTable('drivers')) {
            return;
        }

        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'gender')) {
                $table->dropColumn('gender');
            }
        });
    }
}
