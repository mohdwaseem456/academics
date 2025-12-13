<?php

namespace App\Jobs;

use App\Mail\StudentRegisterStatusMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRegisterStatusMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $student;
    public $status;
    
    public function __construct($student, $status)
    {
        $this->student = $student;
        $this->status = $status;
    }

    public function handle()
    {
        Mail::to($this->student->email)
            ->send(new StudentRegisterStatusMail($this->student, $this->status));
    }
}
