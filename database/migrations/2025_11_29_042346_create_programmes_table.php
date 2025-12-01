<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProgrammesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('programmes', function (Blueprint $table) {
            $table->id(); // id [cite: 24]
            $table->string('name', 150); // name [cite: 24]
            
            // department_id (FK) [cite: 24]
            $table->foreignId('department_id')
                  ->constrained('departments') // Assumes 'departments' table has a standard 'id' PK
                  ->onDelete('restrict');
            
            $table->timestamps();

            $table->unique(['name', 'department_id']); // Ensure program names are unique within a department
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('programmes');
    }
}
