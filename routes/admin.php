<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\AssignmentController;

Route::prefix('admin')->group(function () {
    route::get('/dashboard', function () {
        return view('admin.pages.dashboard');
    })->name('admin.dashboard');
});
