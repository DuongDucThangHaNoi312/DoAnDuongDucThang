<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBasicSalaryTvBasicSalaryHdToPayrollUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payroll_user', function (Blueprint $table) {
            $table->integer('basic_salary_tv');
            $table->integer('basic_salary_hd');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payroll_user', function (Blueprint $table) {
            $table->dropColumn('basic_salary_tv');
            $table->dropColumn('basic_salary_hd');
        });
    }
}
