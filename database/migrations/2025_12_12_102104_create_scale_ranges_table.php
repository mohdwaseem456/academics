<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScaleRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scale_ranges', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('scale_id')
                   ->constrained('scales')
                   ->onDelete('cascade') ;

            $table->integer('%_from');
            $table->integer('%_to');
            $table->string('grade');
                
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scale_ranges');
    }
}
