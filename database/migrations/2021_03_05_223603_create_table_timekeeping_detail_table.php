<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTimekeepingDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timekeeping_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('timekeeping_id');
            $table->unsignedInteger('staff_id');
            $table->integer('code');
            $table->text('detail');
            $table->integer('total');
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
        Schema::dropIfExists('timekeeping_detail');
    }
}
