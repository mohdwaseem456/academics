<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id(); // id (Primary Key)
            
            $table->string('first_name', 100); // first_name
            $table->string('last_name', 100)->nullable(); // last_name

            $table->string('phone_no', 20)->unique(); // phone_no (Must be unique)
            
            // programme_id (Foreign Key to the Programmes table)
            // Uses constrained to ensure data type matches programmes.id
            $table->foreignId('programme_id')
                  ->constrained('programmes')
                  ->onDelete('restrict'); // Prevent deleting a programme with pending apps
            
            // status: 0=Pending (default), 1=Approved, 2=Rejected
            $table->unsignedTinyInteger('status')->default(0)->comment('0=Pending, 1=Approved, 2=Rejected');
            
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
        Schema::dropIfExists('student_registration');
    }
}
