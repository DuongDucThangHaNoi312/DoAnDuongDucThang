<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableWorkScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('department_id');
            $table->string('from_morning', 255);
            $table->string('to_morning', 255);
            $table->string('from_afternoon', 255);
            $table->string('to_afternoon', 255);
            $table->string('from_sa_morning', 255)->nullable();
            $table->string('to_sa_morning', 255)->nullable();
            $table->string('from_sa_afternoon', 255)->nullable();
            $table->string('to_sa_afternoon', 255)->nullable();
            $table->string('overtime');
            $table->integer('created_by');
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
        Schema::dropIfExists('work_schedule');
    }
}
