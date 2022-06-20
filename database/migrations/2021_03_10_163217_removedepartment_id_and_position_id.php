<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemovedepartmentIdAndPositionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (Schema::hasColumn('targets', 'department_id')){

            Schema::table('targets', function (Blueprint $table) {
                $table->dropColumn('department_id');

            });
        }
        if (Schema::hasColumn('targets', 'position_id')){

            Schema::table('targets', function (Blueprint $table) {
                $table->dropColumn('position_id');

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
