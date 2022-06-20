<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_times', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id');
            $table->integer('department_id');
            $table->string('shift1_in');
            $table->string('shift1_out');
            $table->string('shift2_in');
            $table->string('shift2_out');
            $table->string('shift3_in');
            $table->string('shift3_out');
            $table->string('created_by');
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
        Schema::dropIfExists('shift_times');
    }
}
