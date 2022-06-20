<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColunmsNameEsAndMoreToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
	public function up()
	{
		Schema::table('companies', function (Blueprint $table) {
			$table->string('name_es')->after('shortened_name');
			$table->string('address_es')->after('address');
			$table->smallInteger('user_id')->after('tax_code');
			$table->smallInteger('qualification_id')->after('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('companies', function (Blueprint $table) {
			$table->dropColumn('name_es');
			$table->dropColumn('address_es');
			$table->dropColumn('user_id');
			$table->dropColumn('qualification_id');
		});
	}
}
