<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_mark', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paper_assessment_id')
                    ->constrained('paper_assessment')
                    ->onDelete('cascade'); 
                    
            $table->unsignedBigInteger('student_id');
            
            $table->foreign('student_id')
                ->references('student_id')     
                ->on('students')
                ->onDelete('cascade'); 

             $table->unsignedInteger('mark') ;    

            


            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_mark');
    }
}
