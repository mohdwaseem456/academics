<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('user_id'); // Primary Key (PK) column 
            $table->unsignedTinyInteger('user_type')->comment('1=Student, 2=Faculty'); // User type 
            $table->string('email')->unique(); // Email 
            $table->string('password'); // Password 
            $table->unsignedTinyInteger('status'); // Status 
            $table->timestamps(); // Timestamps for created_at and updated_at 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}