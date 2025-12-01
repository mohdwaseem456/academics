<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmissionsTable extends Migration
{
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id(); // id [cite: 31]
            
            // student_id (FK) [cite: 32] - Links to the Student table's PK (which is user_id)
            $table->foreignId('student_id')
                  ->constrained('students', 'student_id')
                  ->onDelete('cascade');
            
            // batch_id (FK) [cite: 33]
            $table->foreignId('batch_id')
                  ->constrained('batches')
                  ->onDelete('restrict'); 

            $table->string('admission_number', 50)->unique(); // admission_number (unique) [cite: 34]
            $table->string('roll_number', 50); // roll_number (unique per batch) [cite: 35]
            
            $table->timestamps();

            // Constraint: student + batch must be unique 
            $table->unique(['student_id', 'batch_id']);

            // Constraint: roll_number unique within batch 
            $table->unique(['roll_number', 'batch_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admissions');
    }
}