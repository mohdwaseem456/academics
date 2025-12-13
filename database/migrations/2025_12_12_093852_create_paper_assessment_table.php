<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaperAssessmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_assessment', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paper_id')
                    ->constrained('papers')
                    ->onDelete('cascade'); 

            $table->foreignId('assessment_type_id')
                    ->constrained('assessment_types')
                    ->onDelete('cascade');  
                    
            
            $table->foreignId('scale_id')
                    ->constrained('scales')
                    ->onDelete('cascade');         

            $table->integer('max_mark') ;


            $table->unique(['paper_id', 'assessment_type_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paper_assessment');
    }
}
