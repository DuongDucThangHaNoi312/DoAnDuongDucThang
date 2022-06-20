<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompanyIdAndDepartmentToAllowanceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_categories', function (Blueprint $table) {
            $table->integer('company_id')->nullable();
            $table->string('department')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowance_categories', function (Blueprint $table) {
            $table->dropColumn('company_id');
            $table->dropColumn('department');
        });
    }
}
