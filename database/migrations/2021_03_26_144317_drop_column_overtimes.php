<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnOvertimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('overtimes', 'start_time')){

            Schema::table('overtimes', function (Blueprint $table) {
                $table->dropColumn('start_time');

            });
        }
        if (Schema::hasColumn('overtimes', 'end_time')){

            Schema::table('overtimes', function (Blueprint $table) {
                $table->dropColumn('end_time');

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
        //
    }
}
