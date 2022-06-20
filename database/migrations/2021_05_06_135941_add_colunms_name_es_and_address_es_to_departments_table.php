<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmsNameEsAndAddressEsToDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('departments', function (Blueprint $table) {
			$table->string('name_es')->after('name');
			$table->string('address_es')->after('address');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('departments', function (Blueprint $table) {
			$table->dropColumn('name_es');
			$table->dropColumn('address_es');

		});
	}
}
