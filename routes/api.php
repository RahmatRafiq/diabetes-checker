<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\MedicalRecordController;

Route::post('/login', [AuthController::class, 'login'])->name('api.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
    Route::get('/patient/profile', [PatientController::class, 'show'])->name('patient.profile.show');
    Route::put('/patient/profile', [PatientController::class, 'updateProfile'])->name('patient.profile.update');
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::post('/medical-records', [MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::get('/medical-records/{id}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
});

Route::get('/medical-records-test', [MedicalRecordController::class, 'apiTest'])->name('medical-records.apiTest');
Route::get('/patients-test', [PatientController::class, 'apiTest'])->name('patients.apiTest');