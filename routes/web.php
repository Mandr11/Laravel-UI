<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('patients.index');
});

Route::resource('patients', PatientController::class);

// API routes for address dropdown
Route::get('/api/kota/{provinsi}', [PatientController::class, 'getKota']);
Route::get('/api/kecamatan/{kota}', [PatientController::class, 'getKecamatan']);
Route::get('/api/desa/{kecamatan}', [PatientController::class, 'getDesa']);