<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecruitmentProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recruitment_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',100);
            $table->date('dob');
            $table->tinyInteger('gender');
            $table->string('id_card_no',15);
            $table->string('email',100);
            $table->string('telephone',10);
            $table->tinyInteger('education_level');
            $table->string('permanent_residence',200);
            $table->text('description')->nullable();
            $table->unsignedInteger('company_id');
            $table->unsignedInteger('department_id');
            $table->string('recruitment_address',300);
            $table->unsignedInteger('title_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recruitment_profiles');
    }
}
