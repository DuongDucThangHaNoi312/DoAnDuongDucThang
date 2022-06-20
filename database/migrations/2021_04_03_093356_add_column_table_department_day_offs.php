<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTableDepartmentDayOffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_day_offs', function (Blueprint $table) {
            $table->smallInteger('status')->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_day_offs', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
