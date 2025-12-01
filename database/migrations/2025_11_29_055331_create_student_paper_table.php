<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentPaperTable extends Migration
{
    public function up()
    {
        Schema::create('student_paper', function (Blueprint $table) {
            $table->id(); // id [cite: 47]
            
            // student_id (FK) [cite: 48]
            $table->foreignId('student_id')
                  ->constrained('students', 'student_id')
                  ->onDelete('cascade');

            // paper_id (FK) [cite: 49]
            $table->foreignId('paper_id')
                  ->constrained('papers')
                  ->onDelete('cascade');
            
            $table->unsignedTinyInteger('status'); // status [cite: 50]
            $table->timestamps();

            // A student can only enroll in a paper once
            $table->unique(['student_id', 'paper_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_paper');
    }
}