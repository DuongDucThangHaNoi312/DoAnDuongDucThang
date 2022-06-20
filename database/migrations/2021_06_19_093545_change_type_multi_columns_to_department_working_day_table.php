<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeMultiColumnsToDepartmentWorkingDayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_working_day', function (Blueprint $table) {
            $table->unsignedInteger('department_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_working_day', function (Blueprint $table) {
            $table->tinyInteger('department_id')->change();
        });
    }
}
