<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAllowanceActiveAndSalaryActiveToAppendixAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appendix_allowances', function (Blueprint $table) {
			$table->smallInteger('allowance_active')->default(1);
			$table->smallInteger('salary_active')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('appendix_allowances', function (Blueprint $table) {
            //
        });
    }
}
