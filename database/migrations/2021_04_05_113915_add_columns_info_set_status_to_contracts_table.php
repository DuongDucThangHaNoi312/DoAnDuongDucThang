<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsInfoSetStatusToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('set_valid_by')->nullable();
			$table->dateTime('set_valid_on')->nullable();
			$table->unsignedInteger('set_notvalid_by')->nullable();
            $table->dateTime('set_notvalid_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('set_valid_by');
            $table->dropColumn('set_valid_on');
            $table->dropColumn('set_notvalid_by');
            $table->dropColumn('set_notvalid_on');
        });
    }
}
