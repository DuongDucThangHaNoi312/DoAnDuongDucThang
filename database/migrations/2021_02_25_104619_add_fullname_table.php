<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFullnameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('users', 'code')
		&& !Schema::hasColumn('users', 'addresses')
		&& !Schema::hasColumn('users', 'nationality')
		&& !Schema::hasColumn('users', 'gender')
		&& !Schema::hasColumn('users', 'id_card_no')
	    && !Schema::hasColumn('users', 'date_of_birth')
		&& !Schema::hasColumn('users', 'issued_on')
		&& !Schema::hasColumn('users', 'issued_at')
		&& !Schema::hasColumn('users', 'status')) {
			Schema::table('users', function (Blueprint $table) {
				$table->string('code')->nullable();
				$table->string('addresses')->nullable();
				$table->string('nationality')->nullable();
				$table->tinyInteger('gender')->nullable();
				$table->string('id_card_no')->nullable();
				$table->date('date_of_birth')->nullable();
				$table->date('issued_on')->nullable();
				$table->string('issued_at')->nullable();
				$table->tinyInteger('status')->nullable();
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
            $table->dropColumn('code');
            $table->dropColumn('addresses');
            $table->dropColumn('nationality');
            $table->dropColumn('gender');
            $table->dropColumn('id_card_no');
            $table->dropColumn('date_of_birth');
            $table->dropColumn('issued_on');
            $table->dropColumn('issued_at');
            $table->dropColumn('status');
        });
    }
}
