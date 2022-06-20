<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullColumnNameEsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name_es')->nullable()->change();
            $table->string('address_es')->nullable()->change();
            $table->smallInteger('user_id')->nullable()->change();
            $table->smallInteger('qualification_id')->nullable()->change();

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
            $table->string('name_es')->nullable(false)->change();
            $table->string('address_es')->nullable(false)->change();
            $table->smallInteger('user_id')->nullable(false)->change();
            $table->smallInteger('qualification_id')->nullable(false)->change();
        });
    }
}
