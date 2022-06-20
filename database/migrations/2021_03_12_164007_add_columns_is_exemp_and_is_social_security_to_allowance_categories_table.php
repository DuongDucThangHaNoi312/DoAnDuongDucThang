<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsIsExempAndIsSocialSecurityToAllowanceCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allowance_categories', function (Blueprint $table) {
            $table->smallInteger('is_exemp')->default(0);
            $table->smallInteger('is_social_security')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allowance_categories', function (Blueprint $table) {
            $table->dropColumn('is_exemp');
            $table->dropColumn('is_social_security');
        });
    }
}
