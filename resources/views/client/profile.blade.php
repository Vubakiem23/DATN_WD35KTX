@extends('client.layouts.app')

@section('title', 'Thông tin cá nhân - Sinh viên')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">
            <i class="fas fa-user text-primary me-2"></i>
            Thông tin cá nhân
        </h2>
    </div>
</div>

@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                <h4 class="text-info">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                <p class="text-muted mb-4">Vui lòng nộp hồ sơ để xem thông tin cá nhân của bạn.</p>
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Thông tin tài khoản</h6>
                    </div>
                    <div class="card-body text-start">
                        <p><strong>Họ tên:</strong> {{ $user->name ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết thông tin
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Mã sinh viên:</strong> {{ $sinhVien->ma_sinh_vien }}</p>
                        <p><strong>Họ tên:</strong> {{ $sinhVien->ho_ten }}</p>
                        <p><strong>Ngày sinh:</strong> {{ $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('d/m/Y') : 'N/A' }}</p>
                        <p><strong>Giới tính:</strong> {{ $sinhVien->gioi_tinh }}</p>
                        <p><strong>Quê quán:</strong> {{ $sinhVien->que_quan }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nơi ở hiện tại:</strong> {{ $sinhVien->noi_o_hien_tai }}</p>
                        <p><strong>Lớp:</strong> {{ $sinhVien->lop }}</p>
                        <p><strong>Ngành:</strong> {{ $sinhVien->nganh }}</p>
                        <p><strong>Khóa học:</strong> {{ $sinhVien->khoa_hoc }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $sinhVien->so_dien_thoai }}</p>
                        <p><strong>Email:</strong> {{ $sinhVien->email }}</p>
                    </div>
                </div>
                
                @if($sinhVien->citizen_id_number)
                <hr>
                <h6 class="mb-3">Thông tin CMND/CCCD</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Số CMND/CCCD:</strong> {{ $sinhVien->citizen_id_number }}</p>
                        <p><strong>Ngày cấp:</strong> {{ $sinhVien->citizen_issue_date ? $sinhVien->citizen_issue_date->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Nơi cấp:</strong> {{ $sinhVien->citizen_issue_place }}</p>
                    </div>
                </div>
                @endif
                
                @if($sinhVien->guardian_name)
                <hr>
                <h6 class="mb-3">Thông tin người giám hộ</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Họ tên:</strong> {{ $sinhVien->guardian_name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $sinhVien->guardian_phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Quan hệ:</strong> {{ $sinhVien->guardian_relationship }}</p>
                    </div>
                </div>
                @endif
                
                <hr>
                <h6 class="mb-3">Vi phạm của sinh viên</h6>
                @php
                    $violations = $sinhVien->violations()->with('type')->latest('occurred_at')->get();
                @endphp
                @if($violations->count())
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại vi phạm</th>
                                <th>Trạng thái</th>
                                <th>Tiền phạt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $vp)
                            <tr>
                                <td>{{ optional($vp->occurred_at)->format('d/m/Y') ?? 'N/A' }}</td>
                                <td>{{ $vp->type->name ?? 'Không rõ' }}</td>
                                <td>
                                    @php
                                        $status = strtolower((string)$vp->status);
                                        $processed = in_array($status, ['resolved','paid'], true);
                                    @endphp
                                    <span class="badge bg-{{ $processed ? 'success' : 'warning' }}">
                                        {{ $processed ? 'Đã xử lý' : 'Chưa xử lý' }}
                                    </span>
                                </td>
                                <td>{{ is_null($vp->penalty_amount) ? '0' : number_format((float)$vp->penalty_amount, 0, ',', '.') }} đ</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted fst-italic">Chưa có vi phạm nào.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-image me-2"></i>
                    Ảnh đại diện
                </h5>
            </div>
            <div class="card-body text-center">
                @if($sinhVien->anh_sinh_vien)
                    <img src="{{ asset('storage/' . $sinhVien->anh_sinh_vien) }}" 
                         alt="Ảnh sinh viên" 
                         class="img-fluid rounded mb-3"
                         style="max-height: 300px;">
                @else
                    <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                    <p class="text-muted">Chưa có ảnh</p>
                @endif
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Trạng thái hồ sơ
                </h5>
            </div>
            <div class="card-body">
                <p class="mb-0">
                    <strong>Trạng thái:</strong>
                    <span class="badge bg-{{ $sinhVien->trang_thai_ho_so == 'Đã duyệt' ? 'success' : 'warning' }}">
                        {{ $sinhVien->trang_thai_ho_so ?? 'Chờ duyệt' }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
