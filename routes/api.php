<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\StudentPaperController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\PaperController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
                            //AUTHENTICATION

Route::post('login', [AuthController::class, 'login']);

// Protected route group
Route::middleware('auth:api')->group(function () {
    // Logout API: Revokes the currently used token
    Route::post('logout', [AuthController::class, 'logout']);
});


                            

Route::post('registrations', [StudentRegistrationController::class, 'signup']);



Route::middleware('auth:api')->group(function () {
   
    Route::middleware('scope:faculty-access')->group(function () {
        
        
        Route::get('registrations', [FacultyController::class, 'showRegistrations']);
        Route::put('registrations/{id}/approval', [FacultyController::class, 'approveStudent']);
        Route::put('registrations/{id}/rejection', [FacultyController::class, 'rejectStudent']);
        Route::post('/admission', [AdmissionController::class, 'giveAdmission']);
        Route::post('student_paper', [StudentPaperController::class, 'assign']);
        Route::post('/attendances', [AttendanceController::class, 'bulkMark']);
        Route::get('/batchattendances', [AttendanceController::class, 'showBatchAttendance']);
        Route::get('/programmeattendances', [AttendanceController::class, 'showProgrammeAttendance']);
        Route::get('/programmestudents', [ProgrammeController::class, 'showProgrammeStudents']);
                                      ///////
        Route::post('/paper_assessments', [PaperController::class, 'assignAssessment']);
        Route::post('/mark_assessment', [PaperController::class, 'markEntry']);
        Route::get('/mark_assessment', [PaperController::class, 'showMarklist']);
        Route::post('/mark_paper', [PaperController::class, 'markFinalise']);

        
    });
});
