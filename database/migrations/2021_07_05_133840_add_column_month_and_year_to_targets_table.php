<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnMonthAndYearToTargetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('targets', 'month')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->unsignedTinyInteger('month');
            });
        }
        if (!Schema::hasColumn('targets', 'year')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->unsignedSmallInteger('year');
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
        if (Schema::hasColumn('targets', 'month')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->dropColumn('month');
            });
        }
        if (Schema::hasColumn('targets', 'year')) {
            Schema::table('targets', function (Blueprint $table) {
                $table->dropColumn('year');
            });
        }
    }
}
