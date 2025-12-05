@extends('client.layouts.app')

@section('title', 'Lịch bảo trì tài sản - Sinh viên')

@section('content')
@push('styles')
<style>
    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        height: 42px;
        padding: 0 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.95rem;
    }
    .btn-pay {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        height: 42px;
        padding: 0 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.95rem;
    }
</style>
@endpush
<!-- Header màu xanh đậm -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-center align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-wrench me-2"></i>
            Lịch bảo trì tài sản
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
                <p class="text-muted">Vui lòng nộp hồ sơ để xem lịch bảo trì tài sản.</p>
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
{{-- Tab Navigation --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list me-2"></i>
                    Danh sách bảo trì
                </h5>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="baotriTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ ($tab ?? 'dang-xu-ly') === 'dang-xu-ly' ? 'active' : '' }}"
                            href="{{ route('client.lichbaotri.index', ['tab' => 'dang-xu-ly']) }}">
                            <i class="fas fa-clock me-2"></i>
                            Đang xử lý
                            @if(isset($dangXuLyCount) && $dangXuLyCount > 0)
                            <span class="badge bg-warning ms-2">{{ $dangXuLyCount }}</span>
                            @elseif(isset($dangXuLy) && $dangXuLy && $dangXuLy->total() > 0)
                            <span class="badge bg-warning ms-2">{{ $dangXuLy->total() }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link {{ ($tab ?? '') === 'da-hoan-thanh' ? 'active' : '' }}"
                            href="{{ route('client.lichbaotri.index', ['tab' => 'da-hoan-thanh']) }}">
                            <i class="fas fa-check-circle me-2"></i>
                            Đã hoàn thành
                            @if(isset($daHoanThanhCount) && $daHoanThanhCount > 0)
                            <span class="badge bg-success ms-2">{{ $daHoanThanhCount }}</span>
                            @elseif(isset($daHoanThanh) && $daHoanThanh && $daHoanThanh->total() > 0)
                            <span class="badge bg-success ms-2">{{ $daHoanThanh->total() }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- Content --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body p-0">
                @if(($tab ?? 'dang-xu-ly') === 'da-hoan-thanh')
                {{-- Tab Đã hoàn thành --}}
                @if(isset($daHoanThanh) && $daHoanThanh && $daHoanThanh->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fit text-center">ID</th>
                                <th class="fit text-center">Hình ảnh</th>
                                <th>Tài sản</th>
                                <th class="fit text-center col-ngay-bao-tri">Ngày bảo trì</th>
                                <th class="fit text-center col-ngay-hoan-thanh">Ngày hoàn thành</th>
                                <th class="col-mo-ta">Mô tả</th>
                                <th class="fit">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daHoanThanh as $lich)
                            <tr>
                                <td class="text-center">{{ $lich->id }}</td>
                                <td class="text-center">
                                    @if($lich->hinh_anh && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh)))
                                    <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh) }}"
                                        alt="Ảnh sau bảo trì"
                                        title="Click để xem ảnh lớn hơn"
                                        style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;cursor:pointer;transition:transform 0.2s;"
                                        onmouseover="this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.transform='scale(1)'"
                                        onclick="showImageModal('{{ asset('uploads/lichbaotri/'.$lich->hinh_anh) }}', 'Ảnh sau bảo trì')">
                                    @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                                        style="width:70px;height:70px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($lich->taiSan)
                                    <strong>{{ $lich->taiSan->ten_tai_san ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>
                                        Mã: {{ $lich->taiSan->khoTaiSan->ma_tai_san ?? 'N/A' }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-door-open me-1"></i>
                                        {{ $lich->taiSan->phong->ten_phong ?? 'N/A' }}
                                    </small>
                                    @elseif($lich->khoTaiSan)
                                    <strong>{{ $lich->khoTaiSan->ten_tai_san ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>
                                        Mã: {{ $lich->khoTaiSan->ma_tai_san ?? 'N/A' }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-warehouse me-1"></i>
                                        Kho tài sản
                                    </small>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center col-ngay-bao-tri" style="white-space: nowrap;">
                                    {{ $lich->ngay_bao_tri ? \Carbon\Carbon::parse($lich->ngay_bao_tri)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="text-center col-ngay-hoan-thanh" style="white-space: nowrap;">
                                    {{ $lich->ngay_hoan_thanh ? \Carbon\Carbon::parse($lich->ngay_hoan_thanh)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="col-mo-ta" style="max-width:300px;">
                                    {{ Str::limit($lich->mo_ta ?? 'Không có mô tả', 100) }}
                                </td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>
                                        {{ $lich->trang_thai }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $daHoanThanh->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-4x mb-3 text-success"></i>
                    <h5>Chưa có bảo trì nào đã hoàn thành</h5>
                    <p>Lịch sử bảo trì đã hoàn thành sẽ hiển thị tại đây.</p>
                </div>
                @endif
                @else
                {{-- Tab Đang xử lý --}}
                @if(isset($dangXuLy) && $dangXuLy && $dangXuLy->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fit text-center">ID</th>
                                <th class="fit text-center">Hình ảnh</th>
                                <th>Tài sản</th>
                                <th class="fit text-center col-ngay-bao-tri">Ngày bảo trì</th>
                                <th class="col-mo-ta">Mô tả</th>
                                <th class="fit">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dangXuLy as $lich)
                            <tr>
                                <td class="text-center">{{ $lich->id }}</td>
                                <td class="text-center">
                                    @if($lich->hinh_anh_truoc && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh_truoc)))
                                    <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh_truoc) }}"
                                        alt="Ảnh trước bảo trì"
                                        title="Click để xem ảnh lớn hơn"
                                        style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;cursor:pointer;transition:transform 0.2s;"
                                        onmouseover="this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.transform='scale(1)'"
                                        onclick="showImageModal('{{ asset('uploads/lichbaotri/'.$lich->hinh_anh_truoc) }}', 'Ảnh trước bảo trì')">
                                    @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                                        style="width:70px;height:70px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($lich->taiSan)
                                    <strong>{{ $lich->taiSan->ten_tai_san ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>
                                        Mã: {{ $lich->taiSan->khoTaiSan->ma_tai_san ?? 'N/A' }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-door-open me-1"></i>
                                        {{ $lich->taiSan->phong->ten_phong ?? 'N/A' }}
                                    </small>
                                    @elseif($lich->khoTaiSan)
                                    <strong>{{ $lich->khoTaiSan->ten_tai_san ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-barcode me-1"></i>
                                        Mã: {{ $lich->khoTaiSan->ma_tai_san ?? 'N/A' }}
                                    </small>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-warehouse me-1"></i>
                                        Kho tài sản
                                    </small>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td class="text-center col-ngay-bao-tri" style="white-space: nowrap;">
                                    {{ $lich->ngay_bao_tri ? \Carbon\Carbon::parse($lich->ngay_bao_tri)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="col-mo-ta" style="max-width:300px;">
                                    {{ Str::limit($lich->mo_ta ?? 'Không có mô tả', 100) }}
                                </td>
                                <td>
                                    @php
                                    $badgeClass = 'bg-secondary';
                                    $icon = 'fa-clock';

                                    if ($lich->trang_thai == 'Đang lên lịch') {
                                    $badgeClass = 'bg-info';
                                    $icon = 'fa-hourglass-half';
                                    } elseif ($lich->trang_thai == 'Chờ bảo trì') {
                                    $badgeClass = 'bg-warning';
                                    $icon = 'fa-clock';
                                    } elseif ($lich->trang_thai == 'Đang bảo trì') {
                                    $badgeClass = 'bg-primary';
                                    $icon = 'fa-tools';
                                    } elseif ($lich->trang_thai == 'Chờ thanh toán') {
                                    $badgeClass = 'bg-danger';
                                    $icon = 'fa-money-bill';
                                    }
                                    @endphp

                                    <div class="d-flex flex-wrap gap-2 align-items-center">
                                        <span class="badge {{ $badgeClass }} badge-status">
                                            <i class="fas {{ $icon }} me-1"></i>
                                            {{ $lich->trang_thai }}
                                        </span>

                                        @if($lich->trang_thai == 'Chờ thanh toán' && $lich->hoaDonBaoTri)
                                        <button class="btn btn-success btn-pay"
                                            data-bs-toggle="modal"
                                            data-bs-target="#paymentModal"
                                            data-route="{{ route('client.hoadon.baotri.thanhtoan', $lich->hoaDonBaoTri->id) }}"
                                            data-amount="{{ $lich->hoaDonBaoTri->chi_phi }}"
                                            data-phuongthuc="{{ $lich->hoaDonBaoTri->phuong_thuc ?? '' }}">
                                            <i class="fas fa-credit-card me-1"></i> Thanh toán
                                        </button>
                                        @endif
                                    </div>

                                </td>

                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $dangXuLy->links() }}
                    </div>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
                    <h5>Không có bảo trì nào đang xử lý</h5>
                    <p>Tất cả tài sản trong phòng của bạn đang hoạt động bình thường.</p>
                </div>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endif
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Cập nhật thanh toán hóa đơn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded" style="background:#e9f2ff;border:1px solid #c8ddff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-bold text-primary">
                                <i class="fa fa-credit-card me-2"></i> Số tiền
                            </div>
                            <div id="paymentAmount" class="fw-bold text-danger fs-4">0 VND</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Phương thức thanh toán</label>
                        <select name="phuong_thuc_thanh_toan" id="paymentMethod" class="form-select" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                        </select>
                    </div>

                    <div id="bankInfo"
                        style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-3">Thông tin chuyển khoản:</h6>
                        <div class="row">
                            <div class="col-md-7">
                                <p class="mb-2"><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
                                <p class="mb-2"><strong>Số tài khoản:</strong> T1209666</p>
                                <p class="mb-0"><strong>Ngân hàng:</strong> Techcombank - Chi nhánh Hà Nội</p>
                            </div>
                            <div class="col-md-5 text-center">
                                <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản"
                                    class="img-fluid rounded border" style="max-width: 150px;">
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="ghi_chu" class="form-label">Ghi chú thanh toán</label>
                        <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="3"
                            placeholder="Nhập ghi chú thanh toán" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="confirmPaymentBtn">
                        <i class="fa fa-check"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentModal = document.getElementById('paymentModal');
        const paymentForm = document.getElementById('paymentForm');
        const paymentAmount = document.getElementById('paymentAmount');
        const paymentMethod = document.getElementById('paymentMethod');
        const bankInfo = document.getElementById('bankInfo');
        const ghiChu = document.getElementById('ghi_chu');

        // Hiển thị bank info khi chọn chuyển khoản
        paymentMethod.addEventListener('change', function() {
            bankInfo.style.display = this.value === 'Chuyển khoản' ? 'block' : 'none';
        });

        // Khi mở modal
        paymentModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const route = button.getAttribute('data-route');
            const amount = button.getAttribute('data-amount');
            const phuongThuc = button.getAttribute('data-phuongthuc');

            // Gán đúng route action
            paymentForm.action = route;

            // Hiển thị số tiền
            paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' VND';

            // Chọn phương thức thanh toán
            paymentMethod.value = phuongThuc ?? '';
            bankInfo.style.display = phuongThuc === 'Chuyển khoản' ? 'block' : 'none';

            // Xóa ghi chú
            ghiChu.value = '';
        });
    });
</script>

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

    .card-header.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
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

    /* Nav Tabs */
    .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        color: #495057;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 24px;
        font-weight: 600;
        font-size: 15px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: transparent;
        margin-right: 5px;
        border-radius: 8px 8px 0 0;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #17a2b8;
        color: #17a2b8;
        background: rgba(23, 162, 184, 0.05);
    }

    .nav-tabs .nav-link.active {
        color: #17a2b8;
        border-bottom-color: #17a2b8;
        background: rgba(23, 162, 184, 0.1);
        font-weight: 700;
    }

    .nav-tabs .nav-link i {
        margin-right: 8px;
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

    /* Đảm bảo cột ngày thẳng hàng */
    .table th.col-ngay-bao-tri,
    .table th.col-ngay-hoan-thanh,
    .table td.col-ngay-bao-tri,
    .table td.col-ngay-hoan-thanh {
        width: 130px;
        min-width: 130px;
        max-width: 130px;
        text-align: center !important;
        vertical-align: middle !important;
        white-space: nowrap;
        padding-left: 15px !important;
        padding-right: 15px !important;
    }

    /* Tạo khoảng cách giữa cột ngày bảo trì và ngày hoàn thành */
    .table td.col-ngay-bao-tri {
        padding-right: 30px !important;
    }

    /* Tạo khoảng cách giữa cột ngày hoàn thành và mô tả */
    .table td.col-ngay-hoan-thanh {
        padding-right: 50px !important;
    }

    /* Tạo khoảng cách cho cột mô tả */
    .table th.col-mo-ta,
    .table td.col-mo-ta {
        padding-left: 50px !important;
        padding-right: 20px !important;
    }

    /* Đảm bảo cột Tài sản có padding phù hợp */
    .table tbody td:nth-child(3) {
        padding-right: 30px !important;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(90deg, rgba(23, 162, 184, 0.03), transparent);
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

    .badge.bg-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%) !important;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    }

    /* Image Styles */
    .table img {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .table img:hover {
        box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
    }

    /* Empty State */
    .text-center.py-5 {
        padding: 60px 20px !important;
    }

    .text-center.py-5 i {
        opacity: 0.5;
        margin-bottom: 20px;
    }

    .text-center.py-5 h5 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .text-center.py-5 p {
        color: #6c757d;
        font-size: 0.95rem;
    }

    /* Card Footer */
    .card-footer {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-top: 2px solid #e9ecef;
        padding: 20px;
        border-radius: 0 0 15px 15px;
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

        .nav-tabs .nav-link {
            padding: 10px 16px;
            font-size: 14px;
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

{{-- Modal xem hình ảnh --}}
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Hình ảnh bảo trì</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid rounded shadow-sm" style="max-height: 70vh;">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showImageModal(imageSrc, title) {
        document.getElementById('imageModalLabel').textContent = title;
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalImage').alt = title;
        var modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }
</script>
@endpush
@endsection