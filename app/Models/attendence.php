<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'date',
        'hour',
        'student_id',
        'paper_id',
        'faculty_id',
        'attendance', // 1=Present, 0=Absent
    ];

    /**
     * Define the constant for Present attendance status.
     */
    const STATUS_PRESENT = 1;

    /**
     * Define the constant for Absent attendance status.
     */
    const STATUS_ABSENT = 0;

    // --- Relationships ---

    /**
     * Get the student associated with the attendance record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    /**
     * Get the paper for which attendance was marked.
     */
    public function paper(): BelongsTo
    {
        return $this->belongsTo(Paper::class);
    }

    /**
     * Get the faculty member who marked the attendance.
     */
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class, 'faculty_id', 'faculty_id');
    }
}