<?php
use App\Modules\Uploads\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::controller(UploadController::class)->group(function() {
    Route::get('/', 'index')->name('upload.index');
    Route::post('/process', 'process')->name('upload.process');
});
