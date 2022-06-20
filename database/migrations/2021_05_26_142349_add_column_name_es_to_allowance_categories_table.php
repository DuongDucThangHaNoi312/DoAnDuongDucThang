<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNameEsToAllowanceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('allowance_categories','name_es')) {
			Schema::table('allowance_categories', function (Blueprint $table) {
				$table->string('name_es')->nullable()->after('name');
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
            $table->dropColumn('name_es');
        });
    }
}
