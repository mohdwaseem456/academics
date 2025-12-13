<p>Hello {{ $student->first_name }},</p>

@if($status == 'approved')
    <p>Your signup has been <strong>APPROVED</strong>. Welcome!</p>
@else
    <p>Your signup has been <strong>REJECTED</strong>.</p>
@endif

<p>Regards,<br>College Admin</p>
