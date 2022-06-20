<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeDeptToAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('allowances', 'type_dept')) {
            Schema::table('allowances', function (Blueprint $table) {
                $table->tinyInteger('type_dept')->nullable();
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
        if (Schema::hasColumn('allowances', 'type_dept')) {
            Schema::table('allowances', function (Blueprint $table) {
                $table->dropColumn('type_dept');
            });
        }
    }
}
