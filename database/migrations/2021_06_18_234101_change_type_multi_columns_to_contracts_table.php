<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeMultiColumnsToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('staff_id')->change();
            $table->unsignedInteger('company_id')->change();
            $table->unsignedInteger('department_id')->change();
            $table->unsignedInteger('title_id')->change();
            $table->unsignedInteger('position_id')->change();
            $table->unsignedInteger('user_id')->change();
            $table->unsignedInteger('qualification_id')->change();
            $table->unsignedInteger('transfer_from')->change();
            $table->unsignedInteger('transfer_to')->change();
            $table->unsignedInteger('department_group_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->tinyInteger('staff_id')->change();
            $table->tinyInteger('company_id')->change();
            $table->tinyInteger('department_id')->change();
            $table->tinyInteger('position_id')->change();
            $table->tinyInteger('title_id')->change();
            $table->smallInteger('user_id')->change();
            $table->smallInteger('qualification_id')->change();
            $table->tinyInteger('transfer_from')->change();
            $table->tinyInteger('transfer_to')->change();
            $table->smallInteger('department_group_id')->change();
        });
    }
}
