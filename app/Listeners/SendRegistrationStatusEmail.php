<?php

namespace App\Listeners;

use App\Events\StudentRegistrationProcessed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationStatusMail;

class SendRegistrationStatusEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(StudentRegistrationProcessed $event)
    {
        Mail::to($event->student->email)
            ->send(new RegistrationStatusMail($event->student, $event->status));
    }
}
