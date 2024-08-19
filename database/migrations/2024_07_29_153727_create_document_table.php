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
            $table->string("bookID");
            $table->string("madeAt");
            $table->string("formula");
            $table->string("last_name");
            $table->string("first_name");
            $table->string("nickname");
            $table->string("dob");
            $table->string("pob_province");
            $table->string("pob_district");
            $table->string("pob_commune");
            $table->string("ethnicity");
            $table->string("nationality");
            $table->string("religion");
            $table->string("previous_occupation");
            $table->string("occupation");
            $table->string("current_address");
            $table->string("province");
            $table->string("district");
            $table->string("commune");
            $table->string("identity");
            $table->string("height");
            $table->string("spouse");
            $table->string("spouse_address");
            $table->string("father_name");
            $table->string("father_address");
            $table->string("mother_name");
            $table->string("mother_address");
            $table->string("private_certificate_officer");
            $table->string("supervision_officer");
            $table->string("scheduling_research_officer");
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
