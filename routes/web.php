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
use App\Http\Controllers\KhuController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\ViolationController;
use App\Http\Controllers\ViolationTypeController;
use App\Http\Controllers\MucDoController;
use App\Http\Controllers\TieuDeController;
use App\Models\Violation;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/



// ADMIN (Chỉ admin mới login được)
Route::get('', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'handle_login'])->name('auth.handle.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');
// REGISTER cho tài khoản thường
// ======================
Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'handle_register'])->name('auth.handle.register');
// =================== 🧑‍🎓 STUDENT ===================
Route::group(['prefix' => 'student', 'middleware' => ['student']], function () {
    Route::get('', [SinhVienController::class, 'index'])->name('student.index');
});
// =================== 🧑‍💼 MANAGER ===================
Route::group(['prefix' => 'manager', 'middleware' => ['manager']], function () {
    Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
});

// =================== 🛠️ ADMIN ===================
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Trang chủ admin
    Route::get('', [AdminController::class, 'index'])->name('admin.index');

    // ---------------- PHÒNG & SLOT ----------------
    Route::resource('phong', PhongController::class);
    Route::post('phong/{phong}/change-status', [PhongController::class, 'changeStatus'])->name('phong.changeStatus');
    Route::post('phong/{phong}/slots', [SlotController::class, 'store'])->name('phong.slots.store');
    Route::post('/slots/{id}/assign', [SlotController::class, 'assignStudent']);
    Route::get('/phong/{id}/slots', [SlotController::class, 'slotsByPhong']);
    Route::post('/slots/{id}/update', [SlotController::class, 'update'])->name('slots.update');
    Route::get('/slots/{id}/assets', [SlotController::class, 'assets'])->name('slots.assets');
    Route::get('/slots/{id}/warehouse-assets', [SlotController::class, 'warehouseAssets'])->name('slots.warehouseAssets');
    Route::post('/slots/{id}/assign-assets', [SlotController::class, 'assignAssets'])->name('slots.assignAssets');
    Route::post('/slots/{id}/clear-assets', [SlotController::class, 'clearAssets'])->name('slots.clearAssets');
    Route::post('/slots/import-from-warehouse', [SlotController::class, 'importFromWarehouse'])->name('slots.importFromWarehouse');
    // ---------------- GÁN PHÒNG ----------------
    // ======================
    Route::get('assign/{svId}', [AssignmentController::class, 'showAssignForm'])->name('assign.form');
    Route::post('assign/{svId}', [AssignmentController::class, 'assign'])->name('assign.do');

    // ---------------- SINH VIÊN ----------------

    Route::resource('sinhvien', SinhVienController::class)->except(['show']);
    Route::patch('sinhvien/{id}/approve', [SinhVienController::class, 'approve'])->name('sinhvien.approve');
    Route::get('sinhvien/show/{id}', [SinhVienController::class, 'show'])->name('sinhvien.show.modal');
    //thongbao
    Route::post('tieude/ajax/create', [TieuDeController::class, 'ajaxCreate'])->name('tieude.ajaxCreate');
    Route::delete('tieude/ajax/delete', [TieuDeController::class, 'ajaxDelete'])->name('tieude.ajaxDelete');

    // ------------------ MỨC ĐỘ ------------------
    // Thêm mới mức độ
    Route::post('/mucdo/ajax/create', [MucDoController::class, 'ajaxCreate'])->name('mucdo.ajaxCreate');
    Route::delete('/mucdo/ajax/delete', [MucDoController::class, 'ajaxDelete'])->name('mucdo.ajaxDelete');

   
    Route::prefix('lichbaotri')->group(function () {
        // CRUD cơ bản
        Route::get('/', [LichBaoTriController::class, 'index'])->name('lichbaotri.index');
        Route::get('/create', [LichBaoTriController::class, 'create'])->name('lichbaotri.create');
        Route::post('/', [LichBaoTriController::class, 'store'])->name('lichbaotri.store');
        Route::get('/{id}/edit', [LichBaoTriController::class, 'edit'])->name('lichbaotri.edit');
        Route::put('/{id}', [LichBaoTriController::class, 'update'])->name('lichbaotri.update');
        Route::delete('/{id}', [LichBaoTriController::class, 'destroy'])->name('lichbaotri.destroy');

        // 👁️ Xem chi tiết (modal)
        Route::get('/show/{id}', [LichBaoTriController::class, 'show'])->name('lichbaotri.show');

        // 🔍 Lấy tài sản theo phòng
        Route::get('/tai-san', [LichBaoTriController::class, 'getTaiSanByPhong'])->name('lichbaotri.taiSanByPhong');

        // ✅ Hoàn thành bảo trì
        Route::get('/hoanthanh/{id}', [LichBaoTriController::class, 'hoanThanhForm'])->name('lichbaotri.hoanthanh.form');
        Route::post('/hoanthanh/{id}', [LichBaoTriController::class, 'hoanThanhSubmit'])->name('lichbaotri.hoanthanh.submit');
        Route::patch('/hoanthanh/{id}', [LichBaoTriController::class, 'hoanThanh'])->name('lichbaotri.hoanthanh');

        Route::get('/get-loai-tai-san', [LichBaoTriController::class, 'getLoaiTaiSan']);
        Route::get('/get-tai-san-kho/{loaiId}', [LichBaoTriController::class, 'getTaiSanKho']);
        Route::get('/get-tai-san-phong/{phongId}', [LichBaoTriController::class, 'getTaiSanPhong']);
    });
     
    Route::prefix('taisan')->group(function () {
        Route::get('', [TaiSanController::class, 'index'])->name('taisan.index');
        Route::get('create', [TaiSanController::class, 'create'])->name('taisan.create');
        Route::post('store', [TaiSanController::class, 'store'])->name('taisan.store');
        Route::get('edit/{id}', [TaiSanController::class, 'edit'])->name('taisan.edit');
        Route::put('update/{id}', [TaiSanController::class, 'update'])->name('taisan.update');
        Route::delete('delete/{id}', [TaiSanController::class, 'destroy'])->name('taisan.destroy');
        Route::put('{id}/baohong', [TaiSanController::class, 'baoHong'])->name('taisan.baohong');
        Route::get('chitiet/{id}', [TaiSanController::class, 'showModal'])->name('taisan.showModal');

        // ✅ Route dùng cho dropdown lọc tài sản theo loại
        Route::get('related/{loai_id}', [TaiSanController::class, 'related'])->name('taisan.related');
        Route::get('taisan/related/{loai_id}', [TaiSanController::class, 'related'])->name('taisan.related');

       Route::get('ajax/get-tai-san-by-loai', [TaiSanController::class, 'ajaxGetTaiSan'])
    ->name('taisan.ajax.getTaiSanByLoai');

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
    // =================== LOẠI TÀI SẢN ===================
    Route::resource('loaitaisan', App\Http\Controllers\LoaiTaiSanController::class);


   
    Route::get('kho/show/{id}', [KhoTaiSanController::class, 'showModal'])->name('kho.show.modal');
    // ======================
    // KHU (Khu vực KTX)
    Route::get('phong/{phong}/taisanphong', [TaiSanController::class, 'byPhong'])->name('taisan.byPhong');
    // Quản lý Khu
    Route::resource('khu', KhuController::class)->except(['edit', 'update', 'destroy']);
});

// ---------------- Sự cố ----------------
        // Resource chính cho sự cố
        Route::resource('suco', SuCoController::class);

        // Xác nhận thanh toán sự cố
        Route::post('suco/{id}/thanh-toan', [SuCoController::class, 'thanhToan'])
            ->name('suco.thanhtoan');

        // Modal Hoàn thành sự cố
        Route::post('suco/{id}/hoan-thanh', [SuCoController::class, 'hoanThanh'])
            ->name('suco.hoanThanh');

        // ----------------- HÓA ĐƠN SỰ CỐ -----------------

        // Danh sách hóa đơn sự cố
        Route::get('hoadon-suco', [\App\Http\Controllers\HoaDonSuCoController::class, 'index'])
            ->name('hoadonsuco.index');

        // Xác nhận thanh toán hóa đơn sự cố
        Route::post('hoadon-suco/{id}/thanhtoan', [\App\Http\Controllers\HoaDonSuCoController::class, 'thanhtoan'])
            ->name('suco.hoadon.thanhtoan');

        // ----------------- FORM THANH TOÁN SỰ CỐ -----------------

        // Form thanh toán hàng loạt cho các sự cố chưa có hóa đơn (admin nhập giá tiền)
        Route::get('admin/suco-thanh-toan', [SuCoController::class, 'formThanhToan'])
            ->name('suco.thanhtoan');

        // Xử lý lưu giá tiền và tạo hóa đơn (admin submit form)
        Route::post('admin/suco-thanh-toan', [SuCoController::class, 'luuThanhToan'])
            ->name('suco.luuThanhtoan');

        // ----------------- TẠO HÓA ĐƠN RIÊNG CHO 1 SỰ CỐ -----------------

        // Form tạo hóa đơn 1 sự cố
        Route::get('admin/suco/{id}/tao-hoa-don', [\App\Http\Controllers\SuCoController::class, 'formTaoHoaDon'])
            ->name('suco.formTaoHoaDon');

        // Lưu hóa đơn sau khi nhập giá tiền
        Route::post('admin/suco/{id}/tao-hoa-don', [\App\Http\Controllers\SuCoController::class, 'luuHoaDon'])
            ->name('suco.luuHoaDon');
        


// ======================
// HÓA ĐƠN
// ======================

Route::prefix('hoadon')->group(function () {
    Route::post('/hoadon/import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
    Route::get('/hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
    Route::delete('/hoadon/{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
    Route::post('/hoadon/thanhtoan/{id}', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');
    Route::get('/hoadon/export', [HoaDonController::class, 'export'])->name('hoadon.export');
    Route::get('/hoadon/lichsu', [HoaDonController::class, 'lichSu'])->name('hoadon.lichsu');
    Route::get('/hoadon/{id}', [HoaDonController::class, 'show'])->name('hoadon.show');
    Route::get('/hoadon/{id}/edit', [HoaDonController::class, 'edit'])->name('hoadon.edit');
    Route::put('/hoadon/{id}', [HoaDonController::class, 'update'])->name('hoadon.update');
    Route::get('/hoadon/{id}/pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
    Route::get('/hoadon/{id}/bienlai', [HoaDonController::class, 'xemBienLai'])->name('hoadon.bienlai');




    Route::prefix('hoadon')->group(function () {
        Route::get('', [HoaDonController::class, 'index'])->name('hoadon.index');
        Route::post('import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
        Route::delete('{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
        Route::get('{id}/export-pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
        Route::get('export-excel/{id}', [HoaDonController::class, 'exportExcelPhong'])->name('hoadon.export_excel_phong');
        Route::post('{id}/thanh-toan', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');
    });
    // TÀI SẢN
    // ======================

    // ======================
    // NGƯỜI DÙNG
    // ======================
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index'])->name('users.index');
        Route::get('create', [UserController::class, 'create'])->name('users.create');
        Route::post('store', [UserController::class, 'store'])->name('users.store');
        Route::get('edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::put('update/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    // ---------------- THÔNG BÁO ----------------


    // ======================
    // SINH VIÊN (Chỉ student mới vào)
    Route::prefix('student')->middleware(['auth', 'student'])->group(function () {
        Route::get('', [SinhVienController::class, 'index'])->name('student.index');
    });

    // ======================
    // QUẢN LÝ (Chỉ manager mới vào)
    Route::prefix('manager')->middleware(['auth', 'manager'])->group(function () {
        Route::get('', [QuanLyController::class, 'index'])->name('manager.index');
    });


    // ---------------- HÓA ĐƠN ----------------
    Route::get('hoadon', [HoaDonController::class, 'index'])->name('hoadon.index');
    Route::post('hoadon/import', [HoaDonController::class, 'importHoaDon'])->name('hoadon.import');
    Route::delete('hoadon/{id}', [HoaDonController::class, 'destroy'])->name('hoadon.destroy');
    Route::get('hoadon/{id}/export-pdf', [HoaDonController::class, 'exportPDF'])->name('hoadon.export_pdf');
    Route::get('hoadon/export-excel/{id}', [HoaDonController::class, 'exportExcelPhong'])->name('hoadon.export_excel_phong');
    Route::post('hoadon/{id}/thanh-toan', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');





    // ---------------- SỰ CỐ ----------------
    Route::resource('suco', SuCoController::class);
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
    // ---------------- TÀI SẢN ----------------

    Route::post('/hoadon/thanhtoan/{id}', [HoaDonController::class, 'thanhtoan'])->name('hoadon.thanhtoan');


    // =================== IMPORT ADMIN EXTRA ===================

    require __DIR__ . '/admin.php';
});
