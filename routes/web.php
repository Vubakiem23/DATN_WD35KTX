<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\LichBaoTriController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::resource('phong', PhongController::class)->except(['show']);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');

    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');
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

/* tài sản */
Route::prefix('taisan')->group(function () {
    Route::get('/', [TaiSanController::class, 'index'])->name('taisan.index');
    Route::get('/create', [TaiSanController::class, 'create'])->name('taisan.create');
    Route::post('/store', [TaiSanController::class, 'store'])->name('taisan.store');
    Route::get('/edit/{id}', [TaiSanController::class, 'edit'])->name('taisan.edit');
    Route::put('/update/{id}', [TaiSanController::class, 'update'])->name('taisan.update');
    Route::delete('/delete/{id}', [TaiSanController::class, 'destroy'])->name('taisan.destroy');
    Route::put('/{id}/baohong', [TaiSanController::class, 'baoHong'])->name('taisan.baohong');
});

/* lịch bảo trì */
Route::resource('lichbaotri', LichBaoTriController::class);

// 🟢 Route bổ sung để "đánh dấu hoàn thành"
Route::patch('/lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])
    ->name('lichbaotri.hoanthanh');

require __DIR__ . '/admin.php';
