<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_user', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('total_salary', 11, 3);
            $table->decimal('total_real_salary', 11, 3);
            $table->decimal('food_allowance_nonTax', 11, 3);
            $table->decimal('food_allowance_tax', 11, 3);
            $table->decimal('total_allowances', 11, 3);
            $table->decimal('basic_salary', 11, 3);
            $table->decimal('salary_bh', 11, 3);
            $table->decimal('working_salary_non_tax', 11, 3);
            $table->decimal('working_salary_tax', 11, 3);
            $table->decimal('salary_ot_non_tax', 11, 3);
            $table->decimal('salary_ot_tax', 11, 3);
            $table->decimal('salary_concurrent', 11, 3);
            $table->text('bh');
            $table->decimal('income_taxes', 11, 3);
            $table->decimal('taxable_income', 11, 3);
            $table->decimal('personal_income_tax', 11, 3);
            $table->decimal('family_allowances', 11, 3);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('payroll_id');
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
        Schema::dropIfExists('payroll_user');
    }
}
