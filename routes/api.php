<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\FacultyController;

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
        Route::put('registrations/{id}/approve', [FacultyController::class, 'approveStudent']);
        Route::put('registrations/{id}/reject', [FacultyController::class, 'rejectStudent']);
        Route::post('/admission', [AdmissionController::class, 'giveAdmission']);


        
    });
});
