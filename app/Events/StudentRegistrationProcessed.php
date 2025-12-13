<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
//use App\Models\Student;

class StudentRegistrationProcessed
{
    use SerializesModels;

    public $student;
    public $status; // 'approved' or 'rejected'

    public function __construct( $student, string $status)
    {
        $this->student = $student;
        $this->status = $status;
    }
}
