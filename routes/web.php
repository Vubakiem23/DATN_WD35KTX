<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\AssignmentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|

*/
Route::prefix('admin')->middleware(['auth'])->group(function(){
    Route::resource('phong', PhongController::class)->except(['show']);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');

    Route::get('assign/{svId}', [AssignmentController::class,'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class,'assign'])->name('assign.do');
});
Route::get('', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'handle_login'])->name('auth.handle.login');
Route::post('/register', [AuthController::class, 'handle_register'])->name('auth.handle.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

/* student route */
Route::group(['prefix' => 'student', 'middleware' => ['student']], function () {
    Route::get('', [SinhVienController::class, 'index'])->name('student.index');

});

/* manager route */
Route::group(['prefix' => 'manager', 'middleware' => ['manager']], function () {
    Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
});


    /* admin route */
    Route::group(['prefix' => 'admin', 'middleware' => ['admin']], function () {
        Route::get('', [AdminController::class, 'index'])->name('admin.index');
    });



require __DIR__ . '/admin.php';
