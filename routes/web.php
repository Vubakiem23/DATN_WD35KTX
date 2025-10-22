<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QuanLyController;
use App\Http\Controllers\SinhVienController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\HoaDonController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\KhoTaiSanController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\LichBaoTriController;
use App\Http\Controllers\ThongBaoController;
use App\Http\Controllers\SuCoController;
use App\Http\Controllers\SlotController;
use App\Models\KhoTaiSan;
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
    Route::resource('phong', PhongController::class)->except([]);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');
    // Tạo slot cho phòng: POST /admin/phong/{phong}/slots
    Route::post('phong/{phong}/slots', [SlotController::class, 'store'])
        ->name('phong.slots.store');
    // tạo slot
    Route::post('/slots/{id}/assign', [SlotController::class, 'assignStudent']); // gán sinh viên
    Route::get('/phong/{id}/slots', [SlotController::class, 'slotsByPhong']);    // lấy sl
    Route::post('/slots/{id}/update', [SlotController::class, 'update'])->name('slots.update');

    // Gán phòng cho sinh viên
    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');

    // Quản lý sinh viên
    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');
    Route::get('/sinhvien/show/{id}', [SinhVienController::class, 'show'])->name('sinhvien.show.modal');


    // Route hóa đơn
    

    Route::post('/hoadon/import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
    Route::get('/hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
    Route::get('/hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
    Route::delete('/hoadon/{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
    Route::get('/hoadon/{id}/export-pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
    Route::get('/hoadon/export-excel/{id}', [HoaDonController::class, 'exportExcelPhong'])->name('hoadon.export_excel_phong');




    // Quản lý sự cố bảo trì
    Route::resource('suco', SuCoController::class);

    // Lịch bảo trì
    Route::resource('lichbaotri', LichBaoTriController::class);
    Route::patch('/lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])
        ->name('lichbaotri.hoanthanh');
    Route::get('/lichbaotri/show/{id}', [LichBaoTriController::class, 'showModal'])
        ->name('lichbaotri.show.modal');

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
        Route::get('/chitiet/{id}', [TaiSanController::class, 'showModal'])->name('taisan.showModal');

    });
    Route::resource('kho', \App\Http\Controllers\KhoTaiSanController::class);
    Route::get('/kho/show/{id}', [KhoTaiSanController::class, 'showModal'])->name('kho.show.modal');

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
