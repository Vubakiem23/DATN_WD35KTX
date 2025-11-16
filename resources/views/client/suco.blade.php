@extends('client.layouts.app')

@section('title', 'Báo sự cố - Sinh viên')

@section('content')
<!-- Header màu xanh đậm -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-center align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Báo sự cố phòng
        </h4>
    </div>
</div>

@if(!$sinhVien)
    {{-- Chưa nộp hồ sơ --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                    <h4 class="text-info">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                    <p class="text-muted">Vui lòng nộp hồ sơ để có thể báo sự cố.</p>
                </div>
            </div>
        </div>
    </div>

@elseif(!$phong)
    {{-- Chưa có phòng --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-door-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa được phân phòng</h4>
                    <p class="text-muted">Vui lòng liên hệ quản trị viên để được phân phòng.</p>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Form và danh sách sự cố --}}
    <div class="row">
        {{-- Form báo sự cố --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-plus-circle me-2"></i> Báo cáo sự cố mới
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('client.suco.store') }}" method="POST" enctype="multipart/form-data" class="flex-fill d-flex flex-column">
                        @csrf
                        <input type="hidden" name="phong_id" value="{{ $phong->id }}">

                        <div class="mb-3">
                            <label class="form-label">Sinh viên</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $sinhVien->ho_ten }} ({{ $sinhVien->ma_sinh_vien }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phòng</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $phong->ten_phong }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả sự cố</label>
                            <textarea name="mo_ta" class="form-control" rows="4" 
                                      placeholder="Nhập mô tả chi tiết..." required>{{ old('mo_ta') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh minh chứng (nếu có)</label>
                            <input type="file" name="anh" class="form-control" accept="image/*">
                        </div>

                        <div class="mt-auto">
                            <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm" style="padding: 12px; font-size: 16px; border-radius: 10px;">
                                <i class="fa fa-paper-plane me-2"></i> Gửi báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Danh sách sự cố --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i> Sự cố gần đây</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:600px; overflow:auto;">
                        @if($dsSuCo->count())
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light position-sticky top-0" style="z-index:1;">
                                    <tr>
                                        <th class="fit text-center">ID</th>
                                        <th class="fit">Ảnh</th>
                                        <th class="fit">Ngày gửi</th>
                                        <th class="fit">Ngày hoàn thành</th>
                                        <th>Mô tả</th>
                                        <th class="fit">Giá tiền</th>
                                        <th class="fit">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dsSuCo as $sc)
                                        <tr>
                                            <td class="text-center">{{ $sc->id }}</td>
                                            <td>
                                                <img src="{{ $sc->display_anh }}" 
                                                     alt="Ảnh sự cố" 
                                                     class="img-thumbnail" 
                                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                            </td>
                                            <td>{{ $sc->ngay_gui?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $sc->ngay_hoan_thanh?->format('d/m/Y') ?? '-' }}</td>
                                            <td style="max-width:200px;">{{ $sc->mo_ta }}</td>
                                            <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount,0,',','.') . ' ₫' : '0 ₫' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $sc->trang_thai == 'Hoàn thành' ? 'success' : 'warning' }}">
                                                    {{ $sc->trang_thai }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-4x mb-3" style="opacity: 0.3;"></i>
                                <div class="fst-italic">Chưa có sự cố nào</div>
                            </div>
                        @endif
                    </div>

                    @if($dsSuCo->count())
                        <div class="mt-2 d-flex justify-content-center p-2">
                            {{ $dsSuCo->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

@push('styles')
<style>
    /* Header */
    .page-header-dark {
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(26, 35, 126, 0.4);
        margin-bottom: 30px;
        overflow: hidden;
    }

    .page-header-dark h4 {
        font-size: 20px;
        letter-spacing: 0.5px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    /* Card Styles */
    .card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15) !important;
    }

    .card-header.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
    }

    .card-header.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
    }

    .card-header h5 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    .card-body {
        padding: 30px;
        background: #ffffff;
    }

    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 0.95rem;
    }

    .form-control {
        border-radius: 10px;
        border: 1px solid #dee2e6;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #ffc107;
        box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
    }

    .form-control.bg-light {
        background-color: #f8f9fa !important;
        cursor: not-allowed;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    /* Button Styles */
    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 193, 7, 0.4);
    }

    /* Table Styles */
    .table {
        margin-bottom: 0;
        border-radius: 0;
    }

    .table thead {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }

    .table th {
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #495057;
        padding: 16px 12px;
        border-bottom: 2px solid #dee2e6;
    }

    .table td {
        font-size: 0.9rem;
        vertical-align: middle;
        padding: 16px 12px;
        border-bottom: 1px solid #f0f0f0;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(90deg, rgba(255, 193, 7, 0.05), transparent);
        transform: translateX(3px);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Badge Styles */
    .badge {
        padding: 8px 16px;
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        letter-spacing: 0.3px;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
        color: #212529 !important;
    }

    /* Image Styles */
    .table img {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table img:hover {
        box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        transform: scale(1.05);
    }

    /* Empty State */
    .text-center.py-5 {
        padding: 60px 20px !important;
    }

    .text-center.py-5 i {
        opacity: 0.3;
        margin-bottom: 20px;
        color: #6c757d;
    }

    .text-center.py-5 h4 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .text-center.py-5 p {
        color: #6c757d;
        font-size: 0.95rem;
    }

    /* Alert Styles */
    .alert {
        border-radius: 10px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    /* File Input */
    .form-control[type="file"] {
        padding: 8px 15px;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .page-header-dark {
            margin-bottom: 20px;
            border-radius: 12px;
        }

        .page-header-dark h4 {
            font-size: 16px;
        }

        .card-body {
            padding: 20px;
        }
    }

    @media (max-width: 768px) {
        .card-header h5 {
            font-size: 16px;
        }

        .table th,
        .table td {
            font-size: 0.8rem;
            padding: 12px 8px;
        }

        .table th {
            font-size: 0.75rem;
        }

        .badge {
            padding: 6px 12px;
            font-size: 0.75rem;
        }
    }
</style>
@endpush
@endsection
