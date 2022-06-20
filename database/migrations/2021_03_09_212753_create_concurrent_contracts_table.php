<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConcurrentContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('concurrent_contracts', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('contract_id');
			$table->unsignedInteger('company_id');
			$table->unsignedInteger('department_id');
			$table->unsignedInteger('position_id');
			$table->unsignedInteger('qualification_id');
			$table->integer('salary');
			$table->date('valid_from');
			$table->date('valid_to')->nullable();
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
        Schema::dropIfExists('concurrent_contracts');
    }
}
