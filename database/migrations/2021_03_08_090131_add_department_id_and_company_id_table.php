<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDepartmentIdAndCompanyIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		if (!Schema::hasColumn('users', 'department_id')
			&& !Schema::hasColumn('users', 'company_id')
			&& !Schema::hasColumn('users', 'code_timekeeping')) {
			Schema::table('users', function (Blueprint $table) {
				$table->tinyInteger('department_id')->nullable();
				$table->tinyInteger('company_id')->nullable();
				$table->integer('code_timekeeping')->nullable();
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
           $table->dropColumn('department_id');
           $table->dropColumn('company_id');
           $table->dropColumn('code_timekeeping');
        });
    }
}
