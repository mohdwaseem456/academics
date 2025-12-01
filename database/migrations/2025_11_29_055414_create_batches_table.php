<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('batches', function (Blueprint $table) {
            $table->id(); // id [cite: 26]
            $table->string('name', 150); // name [cite: 26]

            // programme_id (FK) [cite: 26]
            $table->foreignId('programme_id')
                  ->constrained('programmes')
                  ->onDelete('cascade');
            
            // academic_year_id (FK) [cite: 26]
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->onDelete('restrict');

            $table->timestamps();

            // Unique constraint on name, programme, and year to ensure unique batches
            $table->unique(['name', 'programme_id', 'academic_year_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batches');
    }
}
