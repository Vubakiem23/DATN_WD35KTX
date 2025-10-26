<?php

use Illuminate\Support\Facades\Route;
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
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\ViolationTypeController;
use App\Models\Violation;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =================== 🔐 AUTH ===================
Route::get('', [AuthController::class, 'login'])->name('auth.login');
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'handle_login'])->name('auth.handle.login');
Route::post('/register', [AuthController::class, 'handle_register'])->name('auth.handle.register');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// =================== 🧑‍🎓 STUDENT ===================
Route::group(['prefix' => 'student', 'middleware' => ['student']], function () {
    Route::get('', [SinhVienController::class, 'index'])->name('student.index');
});

// =================== 🧑‍💼 MANAGER ===================
Route::group(['prefix' => 'manager', 'middleware' => ['manager']], function () {
    Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
});

// =================== 🛠️ ADMIN ===================
Route::prefix('admin')->middleware(['auth'])->group(function () {

    // Trang chủ admin
    Route::get('', [AdminController::class, 'index'])->name('admin.index');

    // ---------------- PHÒNG & SLOT ----------------
    Route::resource('phong', PhongController::class);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');
    Route::post('phong/{phong}/slots', [SlotController::class, 'store'])->name('phong.slots.store');
    Route::post('/slots/{id}/assign', [SlotController::class, 'assignStudent']);
    Route::get('/phong/{id}/slots', [SlotController::class, 'slotsByPhong']);
    Route::post('/slots/{id}/update', [SlotController::class, 'update'])->name('slots.update');

    // ---------------- SINH VIÊN ----------------
    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');
    Route::get('sinhvien/show/{id}', [SinhVienController::class, 'show'])->name('sinhvien.show.modal');

    // ---------------- GÁN PHÒNG ----------------
    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');

    // ---------------- HÓA ĐƠN ----------------
    Route::get('hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
    Route::post('hoadon/import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
    Route::delete('hoadon/{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
    Route::get('hoadon/{id}/export-pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
    Route::get('hoadon/export-excel/{id}', [HoaDonController::class, 'exportExcelPhong'])->name('hoadon.export_excel_phong');
    Route::post('hoadon/{id}/thanh-toan', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');

    // ---------------- LỊCH BẢO TRÌ ----------------
    Route::resource('lichbaotri', LichBaoTriController::class);
    Route::patch('lichbaotri/{id}/hoanthanh', [LichBaoTriController::class, 'hoanThanh'])->name('lichbaotri.hoanthanh');
    Route::get('lichbaotri/show/{id}', [LichBaoTriController::class, 'showModal'])->name('lichbaotri.show.modal');
    Route::get('lichbaotri/tai-san', [LichBaoTriController::class, 'getTaiSanByPhong'])->name('lichbaotri.taiSanByPhong');

    // ---------------- THÔNG BÁO ----------------
    Route::resource('thongbao', ThongBaoController::class);

    // ---------------- SỰ CỐ ----------------
    Route::resource('suco', SuCoController::class);

    // ====== VI PHẠM (violations) ======
    Route::resource('vipham', ViolationController::class);
    Route::get('/vipham/{id}', [ViolationController::class, 'show'])->name('vipham.show');

    // đánh dấu đã xử lý
    Route::patch('vipham/{violation}/resolve', [ViolationController::class, 'resolve'])
        ->name('vipham.resolve');

    // ====== LOẠI VI PHẠM (violation_types) ======
    Route::resource('loaivipham', ViolationTypeController::class)->except(['show']);

    // ---------------- QUẢN LÝ TÀI SẢN ----------------
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

    // 📦 Quản lý kho tài sản
    Route::prefix('kho')->group(function () {
        Route::get('/', [KhoTaiSanController::class, 'index'])->name('kho.index');                // danh sách loại tài sản
        Route::get('/related/{loai_id}', [KhoTaiSanController::class, 'related'])->name('kho.related'); // tài sản cùng loại
        Route::get('/create/{loai_id}', [KhoTaiSanController::class, 'create'])->name('kho.create');    // form thêm
        Route::post('/store/{loai_id}', [KhoTaiSanController::class, 'store'])->name('kho.store');      // lưu
        Route::get('/edit/{id}', [KhoTaiSanController::class, 'edit'])->name('kho.edit');       // form sửa
        Route::put('/update/{id}', [KhoTaiSanController::class, 'update'])->name('kho.update');  // lưu sửa

        Route::delete('/delete/{id}', [KhoTaiSanController::class, 'destroy'])->name('kho.destroy');    // xóa
    });
});

// =================== LOẠI TÀI SẢN ===================
Route::resource('loaitaisan', App\Http\Controllers\LoaiTaiSanController::class);

// =================== IMPORT ADMIN EXTRA ===================
require __DIR__ . '/admin.php';
