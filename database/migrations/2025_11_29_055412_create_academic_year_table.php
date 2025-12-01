<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicYearTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('academic_years', function (Blueprint $table) {
            $table->id(); // id [cite: 28]
            $table->string('year', 10)->unique(); // year (e.g. 2024-2025) [cite: 28]
            $table->unsignedTinyInteger('status'); // status [cite: 28]
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
        Schema::dropIfExists('academic_year');
    }
}
