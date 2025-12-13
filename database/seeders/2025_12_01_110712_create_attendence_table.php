<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Core identifiers for the attendance entry
            $table->date('date')->comment('The calendar day of the attendance.');
            $table->unsignedSmallInteger('hour')->comment('The hour of the class/event (e.g., 1 for 1st hour, 2 for 2nd hour).');
            $table->unsignedBigInteger('student_id');

            // Programme identification (Paper or Event)
            $table->unsignedBigInteger('programme_id')->comment('ID of the Paper or Event.');
            $table->unsignedTinyInteger('programme_type')->comment('1=Paper (Academic), 2=Event (Co-curricular).');

            // Specific paper ID (only required if programme_type is 1)
            $table->unsignedBigInteger('paper_id')->nullable()->comment('Redundant if type=1, but helpful for direct lookups. NULL if programme_type is 2 (Event).');

            // Status and tracking
            $table->unsignedTinyInteger('attendance')->comment('0=Absent, 1=Present.');
            $table->unsignedBigInteger('faculty_id')->comment('The faculty member who marked or last updated the attendance.');
            
            $table->timestamps();

            // Foreign Key Constraints (Assuming existence of related tables)
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
            $table->foreign('faculty_id')->references('faculty_id')->on('faculties')->onDelete('restrict'); 
            
            // The unique key prevents duplicate marking for the same student, same hour, same date, and same programme.
            $table->unique(['date', 'hour', 'student_id', 'programme_id', 'programme_type'], 'attendance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};