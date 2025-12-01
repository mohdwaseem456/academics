<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            // student_id (PK, FK users.user_id) 
            // We use foreignId() and constrained() to set the FK.
            // Since student_id is also the primary key and corresponds to users.user_id, we will define it as such.
            $table->foreignId('student_id')
                  ->primary()
                  ->constrained('users', 'user_id') // Links to the user_id column in the 'users' table
                  ->onDelete('cascade'); // Optional: Deletes student record if user is deleted

            // first_name, last_name, etc. 
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('phone_number', 20)->nullable();
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
        Schema::dropIfExists('students');
    }
}
