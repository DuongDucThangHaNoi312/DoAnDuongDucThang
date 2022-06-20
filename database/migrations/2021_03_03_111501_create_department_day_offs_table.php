<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentDayOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('department_day_offs', function (Blueprint $table) {
			$table->increments('id');
			$table->string('type', 255);
			$table->string('time_off_one_day', 255)->nullable();
			$table->date('start_date');
			$table->integer('start_timestamps');
			$table->string('time_off_start_date', 255)->nullable();
			$table->date('end_date')->nullable();
			$table->integer('end_timestamps')->nullable();
			$table->string('time_off_end_date', 255)->nullable();
			$table->text('reason');
			$table->tinyInteger('department_id');
			$table->integer('created_by');
			$table->integer('deleted_by')->nullable();
			$table->dateTime('deleted_at')->nullable();
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
        Schema::dropIfExists('department_day_offs');
    }
}
