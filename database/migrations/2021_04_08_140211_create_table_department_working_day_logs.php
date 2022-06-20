<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDepartmentWorkingDayLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_working_day_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('work_day_id');
            $table->string('field');
            $table->string('old_data');
            $table->string('new_data');
            $table->string('note');
            $table->smallInteger('action_by');
            $table->dateTime('action_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_department_working_day_logs');
    }
}
