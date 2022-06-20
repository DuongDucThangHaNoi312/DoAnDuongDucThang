<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPhoneAndCodeTimekeepingToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('users', 'phone') && !Schema::hasColumn('users', 'code_timekeeping')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('phone');
				$table->string('code_timekeeping')->nullable();
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
            $table->dropColumn('phone');
            $table->dropColumn('code_timekeeping');

        });
    }
}
