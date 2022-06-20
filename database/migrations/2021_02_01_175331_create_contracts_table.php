<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('staff_id');
            $table->string('code')->unique();
            $table->integer('basic_salary');
            $table->unsignedTinyInteger('is_main')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('type');
            $table->unsignedTinyInteger('company_id');
            $table->unsignedTinyInteger('department_id');
            $table->unsignedTinyInteger('title_id');
            $table->unsignedTinyInteger('position_id');
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
