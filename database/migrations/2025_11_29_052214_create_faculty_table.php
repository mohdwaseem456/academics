<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacultyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faculties', function (Blueprint $table) {
            // faculty_id (PK, FK users.user_id) 
            // Defines the primary key and links it to the user_id in the 'users' table.
            $table->foreignId('faculty_id')
                  ->primary()
                  ->constrained('users', 'user_id') 
                  ->onDelete('cascade'); // Ensures faculty record is removed if the corresponding user is deleted

            // name, etc. 
            $table->string('name', 150); 
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
        Schema::dropIfExists('faculties');
    }
}