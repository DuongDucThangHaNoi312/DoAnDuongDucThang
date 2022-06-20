<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveWorkScheduleOvertime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('work_schedule', 'overtime')){
            Schema::table('work_schedule', function (Blueprint $table) {
                $table->dropColumn('overtime');
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
        Schema::table('work_schedule', function($table) {
            $table->integer('overtime');
        });
    }
}
