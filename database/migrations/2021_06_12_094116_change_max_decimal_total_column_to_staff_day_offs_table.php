<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeMaxDecimalTotalColumnToStaffDayOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_day_offs', function (Blueprint $table) {
            $table->decimal('total', 4, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_day_offs', function (Blueprint $table) {
            $table->decimal('total', 2, 1)->change();
        });
    }
}
