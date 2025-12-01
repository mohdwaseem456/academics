<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperFacultyTable extends Migration
{
    public function up()
    {
        Schema::create('paper_faculty', function (Blueprint $table) {
            $table->id(); // id [cite: 43]
            
            // paper_id (FK) [cite: 44]
            $table->foreignId('paper_id')
                  ->constrained('papers')
                  ->onDelete('cascade');

            // faculty_id (FK) [cite: 45] - Links to the faculties table's PK
            $table->foreignId('faculty_id')
                  ->constrained('faculties', 'faculty_id')
                  ->onDelete('cascade');
            
            $table->timestamps();

            // A faculty member can only be assigned to a paper once
            $table->unique(['paper_id', 'faculty_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('paper_faculty');
    }
}