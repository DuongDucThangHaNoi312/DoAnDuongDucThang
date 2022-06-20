<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppendixAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appendix_allowances', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->unsignedInteger('contract_id');
			$table->unsignedInteger('allowance_id');
			$table->integer('expense');
			$table->integer('salary');
			$table->smallInteger('weight_kpi')->nullable();
            $table->date('valid_from');
            $table->date('valid_to');
            $table->smallInteger('status')->default(0);
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
        Schema::dropIfExists('appendix_allowances');
    }
}
