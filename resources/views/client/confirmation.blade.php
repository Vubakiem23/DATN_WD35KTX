@extends('client.layouts.app')

@section('title', 'Xác nhận hồ sơ ký túc xá')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                {{ $sinhVien->trang_thai_ho_so }}
                            </span>
                        </div>
                        <h2 class="h4">Xác nhận hồ sơ ký túc xá</h2>
                        <p class="text-muted mb-0">
                            Hồ sơ của bạn đã được ban quản lý duyệt sơ bộ. Vui lòng kiểm tra lại thông tin cá nhân
                            và xác nhận để hoàn tất quy trình đăng ký.
                        </p>
                    </div>

                    <div class="alert alert-info d-flex align-items-start gap-2">
                        <i class="fa fa-info-circle mt-1"></i>
                        <div>
                            Sau khi xác nhận, trạng thái hồ sơ sẽ chuyển sang <strong>Đã duyệt</strong> và bạn có thể
                            sử dụng đầy đủ các chức năng dành cho sinh viên trong hệ thống.
                        </div>
                    </div>

                    <div class="border rounded-3 p-3 mb-4 bg-light">
                        <h5 class="fw-semibold mb-3">Thông tin tóm tắt</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="small text-muted">Họ và tên</div>
                                <div class="fw-semibold">{{ $sinhVien->ho_ten }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Mã sinh viên</div>
                                <div class="fw-semibold">{{ $sinhVien->ma_sinh_vien }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Lớp / Ngành</div>
                                <div class="fw-semibold">{{ $sinhVien->lop }} / {{ $sinhVien->nganh }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="small text-muted">Email liên hệ</div>
                                <div class="fw-semibold">{{ $sinhVien->email }}</div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('client.confirmation.store') }}" method="POST">
                        @csrf
                        <div class="d-flex justify-content-between flex-wrap gap-2">
                            <a href="{{ route('client.profile.preview') }}" class="btn btn-outline-secondary">
                                <i class="fa fa-user-circle me-1"></i> Xem lại hồ sơ
                            </a>

                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fa fa-check me-1"></i> Tôi xác nhận thông tin là chính xác
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-warning border-0 shadow-sm">
                <strong>Lưu ý:</strong> Nếu thông tin cá nhân của bạn chưa chính xác, vui lòng liên hệ Ban quản lý
                để được hỗ trợ chỉnh sửa trước khi xác nhận.
            </div>
        </div>
    </div>
</div>
@endsection

