<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\ThanhtoanController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\KhoTaiSanController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\LichBaoTriController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\SuCoController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ======================
// AUTH
// ======================
Route::get('', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'handle_login'])->name('auth.handle.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// ======================
// REGISTER cho tài khoản thường
// ======================
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'handle_register'])->name('auth.handle.register');

// ======================
// ADMIN (Chỉ admin mới login được)
// ======================
Route::prefix('admin')->middleware(['auth','admin'])->group(function () {
    Route::get('', [AdminController::class, 'index'])->name('admin.index');

    // ======================
    // SỰ CỐ
    // ======================
    // ⚙️ Giữ nguyên tên route “suco” nhưng trong controller/view gọi view('su_co.xxx')
    Route::resource('suco', SuCoController::class);

    // 🔹 Route xác nhận thanh toán sự cố
    Route::post('suco/{id}/thanhtoan', [SuCoController::class, 'thanhToan'])->name('suco.thanhtoan');

    // ======================
    // PHÒNG
    // ======================
    Route::resource('phong', PhongController::class);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');
    Route::post('phong/{phong}/slots', [SlotController::class, 'store'])->name('phong.slots.store');
    Route::post('slots/{id}/assign', [SlotController::class, 'assignStudent']);
    Route::get('phong/{id}/slots', [SlotController::class, 'slotsByPhong']);
    Route::post('slots/{id}/update', [SlotController::class, 'update'])->name('slots.update');

    // ======================
    // PHÂN PHÒNG SINH VIÊN
    // ======================
    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');

    // ======================
    // SINH VIÊN
    // ======================
    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');
    Route::get('sinhvien/show/{id}', [SinhVienController::class, 'show'])->name('sinhvien.show.modal');

    // ======================
    // HÓA ĐƠN
    // ======================
    Route::prefix('hoadon')->group(function(){
        Route::get('', [HoaDonController::class, 'index'])->name('hoadon.index');
        Route::post('import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
        Route::delete('{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
        Route::get('{id}/export-pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
        Route::get('export-excel/{id}', [HoaDonController::class, 'exportExcelPhong'])->name('hoadon.export_excel_phong');
        Route::post('{id}/thanh-toan', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');
    });

    // ======================
    // LỊCH BẢO TRÌ
    // ======================
    Route::resource('lichbaotri', LichBaoTriController::class);
    Route::patch('lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])->name('lichbaotri.hoanthanh');
    Route::get('lichbaotri/show/{id}', [LichBaoTriController::class, 'showModal'])->name('lichbaotri.show.modal');

    // ======================
    // THÔNG BÁO
    // ======================
    Route::resource('thongbao', ThongBaoController::class);

    // ======================
    // TÀI SẢN
    // ======================
    Route::prefix('taisan')->group(function(){
        Route::get('', [TaiSanController::class, 'index'])->name('taisan.index');
        Route::get('create', [TaiSanController::class, 'create'])->name('taisan.create');
        Route::post('store', [TaiSanController::class, 'store'])->name('taisan.store');
        Route::get('edit/{id}', [TaiSanController::class, 'edit'])->name('taisan.edit');
        Route::put('update/{id}', [TaiSanController::class, 'update'])->name('taisan.update');
        Route::delete('delete/{id}', [TaiSanController::class, 'destroy'])->name('taisan.destroy');
        Route::put('{id}/baohong', [TaiSanController::class, 'baoHong'])->name('taisan.baohong');
        Route::get('chitiet/{id}', [TaiSanController::class, 'showModal'])->name('taisan.showModal');
    });

    // ======================
    // KHO TÀI SẢN
    // ======================
    Route::resource('kho', KhoTaiSanController::class);
    Route::get('kho/show/{id}', [KhoTaiSanController::class, 'showModal'])->name('kho.show.modal');

    // ======================
    // NGƯỜI DÙNG
    // ======================
    Route::prefix('users')->group(function(){
        Route::get('', [UserController::class, 'index'])->name('users.index');
        Route::get('create', [UserController::class, 'create'])->name('users.create');
        Route::post('store', [UserController::class, 'store'])->name('users.store');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});

// ======================
// SINH VIÊN (Chỉ student mới vào)
Route::prefix('student')->middleware(['auth','student'])->group(function(){
    Route::get('', [SinhVienController::class, 'index'])->name('student.index');
});

// ======================
// QUẢN LÝ (Chỉ manager mới vào)
Route::prefix('manager')->middleware(['auth','manager'])->group(function(){
    Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
});

// ======================
require __DIR__ . '/admin.php';
