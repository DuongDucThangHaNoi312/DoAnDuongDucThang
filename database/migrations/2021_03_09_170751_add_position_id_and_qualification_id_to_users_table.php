<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionIdAndQualificationIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('users', 'position_id') && !Schema::hasColumn('users', 'qualification_id')) {
			Schema::table('users', function (Blueprint $table) {
				$table->tinyInteger('position_id')->nullable();
				$table->tinyInteger('qualification_id')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('position_id');
            $table->dropColumn('qualification_id');
        });
    }
}
