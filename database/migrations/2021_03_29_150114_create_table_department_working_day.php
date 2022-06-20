<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDepartmentWorkingDay extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_working_day', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('start_date');
            $table->date('end_date');
            $table->longText('first_shift')->nullable();
            $table->longText('second_shift')->nullable();
            $table->longText('third_shift')->nullable();
            $table->longText('first_shift_and_ot')->nullable();
            $table->longText('second_shift_and_ot')->nullable();
            $table->smallInteger('department_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_department_working_day');
    }
}
