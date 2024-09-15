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
            $table->string('uuid')->unique();
            $table->string("madeAt")->nullable();
            $table->string("number")->nullable();
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
            $table->decimal("height")->nullable();
            $table->string("spouse")->nullable();
            $table->string("spouse_address")->nullable();
            $table->string("father_name")->nullable();
            $table->string("father_address")->nullable();
            $table->string("mother_name")->nullable();
            $table->string("mother_address")->nullable();
            $table->string("private_certificate_officer")->nullable();
            $table->string("supervision_officer")->nullable();
            $table->string("scheduling_research_officer")->nullable();
            $table->string("right_thumb_print")->nullable();
            $table->string("right_index_print")->nullable();
            $table->string("right_middle_print")->nullable();
            $table->string("right_ring_print")->nullable();
            $table->string("right_pinky_print")->nullable();
            $table->string("left_thumb_print")->nullable();
            $table->string("left_index_print")->nullable();
            $table->string("left_middle_print")->nullable();
            $table->string("left_ring_print")->nullable();
            $table->string("left_pinky_print")->nullable();
            $table->string("front_body_photo")->nullable();
            $table->string("right_profile_photo")->nullable();
            $table->string("left_profile_photo")->nullable();
            $table->string("four_left_fingers_print")->nullable();
            $table->string("left_thumb_print01")->nullable();
            $table->string("right_thumb_print01")->nullable();
            $table->string("four_right_fingers_print")->nullable();
            $table->string("left_palm_print")->nullable();
            $table->string("right_palm_print")->nullable();
            $table->string("special_mark1")->nullable();
            $table->string("special_mark2")->nullable();
            $table->string("special_mark3")->nullable();
            $table->unsignedBigInteger("upload_by");
            // Set up the foreign key constraint
            $table->foreign('upload_by')->references('id')->on('users')->onDelete('cascade');
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
