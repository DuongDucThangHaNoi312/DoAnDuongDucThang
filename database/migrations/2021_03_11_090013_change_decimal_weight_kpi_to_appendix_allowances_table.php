<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDecimalWeightKpiToAppendixAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('appendix_allowances', function (Blueprint $table) {
			$table->decimal('weight_kpi', 8, 2)->change();
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
			$table->smallInteger('weight_kpi')->nullable()->change();
        });
    }
}
