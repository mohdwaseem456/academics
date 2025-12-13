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
        // Table to store consolidated daily attendance counts per student.
        Schema::create('daily_attendances', function (Blueprint $table) {
            $table->id();
            $table->date('date')->comment('The day the attendance records belong to.');
            $table->unsignedBigInteger('student_id');
            $table->unsignedSmallInteger('present_count')->default(0)->comment('Total number of present hours/events for the student on this date.');
            $table->unsignedSmallInteger('absent_count')->default(0)->comment('Total number of absent hours/events for the student on this date.');
            $table->timestamps();

            // Unique constraint to prevent duplicate consolidation for a student on a given day.
            $table->unique(['date', 'student_id']);

            // Foreign key constraint (assuming students table exists)
            $table->foreign('student_id')->references('student_id')->on('students')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_attendances');
    }
};