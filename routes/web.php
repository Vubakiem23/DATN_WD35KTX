<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\LichBaoTriController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\SuCoController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Nhóm route admin (đăng nhập mới vào được)
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Trang chủ admin
    Route::get('', [AdminController::class, 'index'])->name('admin.index');
    Route::prefix('admin')->group(function () {
    Route::resource('suco', SuCoController::class);
});

    // Quản lý phòng
    Route::resource('phong', PhongController::class)->except(['show']);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');

    // Gán phòng cho sinh viên
    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');

    // Quản lý sinh viên
    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');

    // Quản lý sự cố bảo trì
    Route::resource('suco', SuCoController::class);

    // Lịch bảo trì
    Route::resource('lichbaotri', LichBaoTriController::class);
    Route::patch('/lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])
        ->name('lichbaotri.hoanthanh');

    // Thông báo
    Route::resource('thongbao', ThongBaoController::class);

    // Quản lý tài sản
    Route::prefix('taisan')->group(function () {
        Route::get('/', [TaiSanController::class, 'index'])->name('taisan.index');
        Route::get('/create', [TaiSanController::class, 'create'])->name('taisan.create');
        Route::post('/store', [TaiSanController::class, 'store'])->name('taisan.store');
        Route::get('/edit/{id}', [TaiSanController::class, 'edit'])->name('taisan.edit');
        Route::put('/update/{id}', [TaiSanController::class, 'update'])->name('taisan.update');
        Route::delete('/delete/{id}', [TaiSanController::class, 'destroy'])->name('taisan.destroy');
        Route::put('/{id}/baohong', [TaiSanController::class, 'baoHong'])->name('taisan.baohong');
    });
});

// Đăng nhập / đăng ký / đăng xuất
Route::get('', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'handle_login'])->name('auth.handle.login');
Route::post('/register', [AuthController::class, 'handle_register'])->name('auth.handle.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Student
Route::group(['prefix' => 'student', 'middleware' => ['student']], function () {
    Route::get('', [SinhVienController::class, 'index'])->name('student.index');
});

// Manager
Route::group(['prefix' => 'manager', 'middleware' => ['manager']], function () {
    Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
});

require __DIR__ . '/admin.php';
