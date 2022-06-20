<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsOriginalRestAndDetptGroupIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'original_rest')) {
            Schema::table('users', function (Blueprint $table) {
                $table->decimal('original_rest', 4, 1)->default(0);
            });
        }
        if (!Schema::hasColumn('users', 'dept_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('dept_group_id')->nullable();
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
        if (Schema::hasColumn('users', 'original_rest')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('original_rest');
            });
        }
        if (Schema::hasColumn('users', 'dept_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dept_group_id');
            });
        }
    }
}
