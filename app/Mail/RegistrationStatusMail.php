<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Student;

class RegistrationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $status;

    public function __construct( $student, string $status)
    {
        $this->student = $student;
        $this->status = $status;
    }

    public function build()
    {
        $subject = $this->status === 'approved' ? 'Your Application is Approved' : 'Your Application is Rejected';

        return $this->subject($subject)
                    ->markdown('emails.registration_status');
    }
}
