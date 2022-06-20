<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnDepartmentDayOffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_day_offs', function(Blueprint $table) {
            $table->renameColumn('time_off_start_date', 'from_type');
            $table->renameColumn('time_off_end_date', 'to_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_day_offs', function(Blueprint $table) {
            $table->renameColumn('from_type', 'time_off_start_date');
            $table->renameColumn('to_type', 'time_off_end_date');
        });
    }
}
