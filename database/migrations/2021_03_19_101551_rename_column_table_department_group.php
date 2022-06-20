<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameColumnTableDepartmentGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_groups', function(Blueprint $table) {
            $table->renameColumn('multi_manager', 'only_manager');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('department_groups', function(Blueprint $table) {
            $table->renameColumn('only_manager', 'multi_manager');
        });
    }
}
