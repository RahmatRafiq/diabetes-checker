<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/admin', [\App\Http\Controllers\DashboardController::class, 'dashboardAdmin'])->name('dashboard.admin');

    Route::resource('mbkm/about-app', \App\Http\Controllers\AboutAppController::class);

    Route::get('admin/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('admin/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('admin/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('profile/upload', [\App\Http\Controllers\ProfileController::class, 'upload'])->name('profile.upload');
    Route::delete('profile/delete-file', [\App\Http\Controllers\ProfileController::class, 'deleteFile'])->name('profile.deleteFile');
    Route::post('/temp/storage', [\App\Http\Controllers\StorageController::class, 'store'])->name('storage.store');
    Route::delete('/temp/storage', [\App\Http\Controllers\StorageController::class, 'destroy'])->name('storage.destroy');
    Route::get('/temp/storage/{path}', [\App\Http\Controllers\StorageController::class, 'show'])->name('storage.show');

    Route::resource('admin/role-permissions/permission', \App\Http\Controllers\RolePermission\PermissionController::class);
    Route::post('admin/role-permissions/permission/json', [\App\Http\Controllers\RolePermission\PermissionController::class, 'json'])->name('permission.json');

    Route::resource('admin/role-permissions/role', \App\Http\Controllers\RolePermission\RoleController::class);
    Route::post('admin/role-permissions/role/json', [\App\Http\Controllers\RolePermission\RoleController::class, 'json'])->name('role.json');

    Route::resource('admin/role-permissions/user', \App\Http\Controllers\UserController::class);
    Route::post('admin/role-permissions/user/json', [\App\Http\Controllers\UserController::class, 'json'])->name('user.json');

    Route::get('/medical-record', [\App\Http\Controllers\MedicalRecordController::class, 'index'])->name('medical-record.index');
    Route::post('/medical-record/json', [\App\Http\Controllers\MedicalRecordController::class, 'json'])->name('medical-record.json');
    Route::get('/medical-record/create', [\App\Http\Controllers\MedicalRecordController::class, 'create'])->name('medical-record.create');
    Route::post('/medical-record/store', [\App\Http\Controllers\MedicalRecordController::class, 'store'])->name('medical-record.store');
    Route::get('/medical-record/result/{kategori}/{hasil}', [\App\Http\Controllers\MedicalRecordController::class, 'result'])->name('medical-record.result');

    Route::get('/patients', [\App\Http\Controllers\PatientController::class, 'index'])->name('patients.index'); // Menampilkan daftar pasien
    Route::post('/patients/json', [\App\Http\Controllers\PatientController::class, 'json'])->name('patients.json');

    Route::get('/patients/create', [\App\Http\Controllers\PatientController::class, 'create'])->name('patients.create'); // Form buat pasien baru
    Route::post('/patients', [\App\Http\Controllers\PatientController::class, 'store'])->name('patients.store'); // Menyimpan pasien baru
    Route::get('/patients/{patient}/edit', [\App\Http\Controllers\PatientController::class, 'edit'])->name('patients.edit'); // Form edit pasien
    Route::put('/patients/{patient}', [\App\Http\Controllers\PatientController::class, 'update'])->name('patients.update'); // Update data pasien
    Route::delete('/patients/{patient}', [\App\Http\Controllers\PatientController::class, 'destroy'])->name('patients.destroy'); // Hapus pasien
    Route::post('/medical-record/upload-foot-photos', [\App\Http\Controllers\PatientController::class, 'uploadFootPhotos'])->name('medical-record.uploadFootPhotos');

    Route::get('/patient/profile', [\App\Http\Controllers\PatientController::class, 'editProfile'])->name('patient.profile.edit');
    Route::post('/patient/profile', [\App\Http\Controllers\PatientController::class, 'updateProfile'])->name('patient.profile.update');

    Route::get('/medical-records', [\App\Http\Controllers\MedicalRecordController::class, 'index'])->name('medical-records.index');

    Route::get('medical-records/{id}/export-pdf', [\App\Http\Controllers\MedicalRecordController::class, 'exportPDF'])->name('medical-records.exportPDF');
    Route::get('/medical-records/{id}', [\App\Http\Controllers\MedicalRecordController::class, 'show'])->name('medical-records.show');

});

require __DIR__ . '/auth.php';
