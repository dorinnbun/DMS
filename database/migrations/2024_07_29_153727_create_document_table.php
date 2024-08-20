<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string("book_id")->nullable();
            $table->string("madeAt")->nullable();
            $table->string("formula")->nullable();
            $table->string("last_name")->nullable();
            $table->string("first_name")->nullable();
            $table->string("nickname")->nullable();
            $table->date("dob")->nullable();
            $table->string("pob_province")->nullable();
            $table->string("pob_district")->nullable();
            $table->string("pob_commune")->nullable();
            $table->string("ethnicity")->nullable();
            $table->string("nationality")->nullable();
            $table->string("religion")->nullable();
            $table->string("previous_occupation")->nullable();
            $table->string("occupation")->nullable();
            $table->string("current_address")->nullable();
            $table->string("province")->nullable();
            $table->string("district")->nullable();
            $table->string("commune")->nullable();
            $table->string("identity")->nullable();
            $table->decimal("height", 2)->nullable();
            $table->string("spouse")->nullable();
            $table->string("spouse_address")->nullable();
            $table->string("father_name")->nullable();
            $table->string("father_address")->nullable();
            $table->string("mother_name")->nullable();
            $table->string("mother_address")->nullable();
            $table->string("private_certificate_officer")->nullable();
            $table->string("supervision_officer")->nullable();
            $table->string("scheduling_research_officer")->nullable();
            $table->string("allLeftFingers")->nullable();
            $table->string("allRightFingers")->nullable();
            $table->string("leftPalmPrint")->nullable();
            $table->string("rightPalmPrint")->nullable();
            $table->string("specialMark")->nullable();
            $table->string("rightSideProfile")->nullable();
            $table->string("leftSideProfile")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};
