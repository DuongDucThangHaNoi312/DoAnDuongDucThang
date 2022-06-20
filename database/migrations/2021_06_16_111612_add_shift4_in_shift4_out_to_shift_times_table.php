<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShift4InShift4OutToShiftTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shift_times', function (Blueprint $table) {
            $table->string('shift4_in')->nullable();
            $table->string('shift4_out')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shift_times', function (Blueprint $table) {
            $table->dropColumn('shift4_in');
            $table->dropColumn('shift4_out');
        });
    }
}
