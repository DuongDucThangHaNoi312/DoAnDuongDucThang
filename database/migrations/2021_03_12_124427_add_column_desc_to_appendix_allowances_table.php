<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDescToAppendixAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if(!Schema::hasColumn('allowance_categories','desc')) {
			Schema::table('allowance_categories', function (Blueprint $table) {
				$table->text('desc')->nullable();
			});
		}
		if(!Schema::hasColumn('allowances','desc')) {
			Schema::table('allowances', function (Blueprint $table) {
				$table->text('desc')->nullable();
			});
		}
		if(!Schema::hasColumn('appendix_allowances','desc')) {
			Schema::table('appendix_allowances', function (Blueprint $table) {
				$table->text('desc')->nullable();
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
		Schema::table('allowance_categories', function (Blueprint $table) {
			$table->dropColumn('desc');
		});
		Schema::table('allowances', function (Blueprint $table) {
			$table->dropColumn('desc');
		});
		Schema::table('appendix_allowances', function (Blueprint $table) {
			$table->dropColumn('desc');
		});
    }
}
