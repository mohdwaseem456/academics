<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DailyAttendanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Consolidate TODAY’S attendance
        $today = now()->toDateString();

        // Fetch grouped attendance directly from DB
        $rows = DB::table('attendances')
            ->select(
                'student_id',
                DB::raw("SUM(attendance = 1) as present_count"),
                DB::raw("SUM(attendance = 0) as absent_count")
            )
            ->whereDate('date', $today)
            ->groupBy('student_id')
            ->get();

        foreach ($rows as $row) {

            // UPSERT INTO daily_attendances
            DB::table('daily_attendances')->updateOrInsert(
                [
                    'date'       => $today,
                    'student_id' => $row->student_id
                ],
                [
                    'present_count' => $row->present_count,
                    'absent_count'  => $row->absent_count,
                    'updated_at'    => now(),
                    'created_at'    => now(),
                ]
            );
        }
    }
}
