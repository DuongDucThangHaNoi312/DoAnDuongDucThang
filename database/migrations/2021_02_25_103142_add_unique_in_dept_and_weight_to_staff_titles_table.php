<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueInDeptAndWeightToStaffTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_titles', function (Blueprint $table) {
            $table->tinyInteger('unique_in_dept');
            $table->tinyInteger('weight');
            $table->tinyInteger('is_system');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_titles', function (Blueprint $table) {
            $table->dropColumn('unique_in_dept');
            $table->dropColumn('weight');
            $table->dropColumn('is_system');
        });
    }
}
