<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStaffDayOffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_day_offs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('staff_id');
            $table->date('start');
            $table->date('end');
            $table->string('from_type');
            $table->string('to_type');
            $table->integer('total');
            $table->text('reason')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('staff_day_offs');
    }
}
