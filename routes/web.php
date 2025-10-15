<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\LichBaoTriController;
use App\Http\Controllers\ThongBaoController;
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

    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');

    // Route hóa đơn
    Route::get('/hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
Route::get('/hoadon/create', [HoaDonController::class, 'create'])->name('hoadon.create');
Route::post('/hoadon', [HoaDonController::class, 'store'])->name('hoadon.store');
Route::get('/hoadon/{id}/edit', [HoaDonController::class, 'edit'])->name('hoadon.edit');
Route::put('/hoadon/{id}', [HoaDonController::class, 'update'])->name('hoadon.update');
Route::delete('/hoadon/{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
Route::post('/hoadon/{id}/duplicate', [HoaDonController::class, 'duplicate'])->name('hoadon.duplicate');
Route::post('/hoadon/{id}/send', [HoaDonController::class, 'send'])->name('hoadon.send');
Route::get('/hoadon/{id}/pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.pdf');
Route::post('/hoadon/{id}/pay', [HoaDonController::class, 'pay'])->name('hoadon.pay');
Route::get('/lich-su-thanh-toan', [HoaDonController::class, 'history'])->name('hoadon.history');
Route::get('/hoadon/{id}/send-mail', [HoaDonController::class, 'sendMail'])->name('hoadon.sendMail');


Route::get('/hoadon/send', [HoaDonController::class, 'send']);



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
// thong bao//
Route::resource('thongbao', ThongBaoController::class);

// 🟢 Route bổ sung để "đánh dấu hoàn thành"
Route::patch('/lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])
    ->name('lichbaotri.hoanthanh');

require __DIR__ . '/admin.php';
