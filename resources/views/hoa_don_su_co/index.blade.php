@extends('admin.layouts.admin')

@section('content')
    <div class="x_panel">
        <div class="x_title d-flex justify-content-between align-items-center flex-wrap">
            <h2><i class="fa fa-file-text-o text-primary"></i> Danh sách hóa đơn sự cố</h2>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('suco.index') }}" class="btn btn-sm btn-outline-secondary mt-2 mt-sm-0">
                    <i class="fa fa-arrow-left"></i> Quay lại sự cố
                </a>
            </div>
        </div>

        <div class="x_content">
            {{-- 📊 Thống kê --}}
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted mb-2">Tổng hóa đơn</h5>
                            <h3 class="mb-0 text-primary">{{ number_format($tong_hoa_don, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted mb-2">Tổng tiền</h5>
                            <h3 class="mb-0 text-info">{{ number_format($tong_tien, 0, ',', '.') }} ₫</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted mb-2">Đã thanh toán</h5>
                            <h3 class="mb-0 text-success">{{ number_format($da_thanh_toan, 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ number_format($tong_tien_da_thu, 0, ',', '.') }} ₫</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center">
                            <h5 class="text-muted mb-2">Chưa thanh toán</h5>
                            <h3 class="mb-0 text-warning">{{ number_format($chua_thanh_toan, 0, ',', '.') }}</h3>
                            <small class="text-muted">{{ number_format($tong_tien_chua_thu, 0, ',', '.') }} ₫</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔍 Tìm kiếm và lọc --}}
            <form method="GET" action="{{ route('hoadonsuco.index') }}" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small">Tìm kiếm</label>
                        <input type="text" name="search" value="{{ request('search') ?? '' }}"
                            class="form-control form-control-sm" placeholder="MSSV hoặc Họ tên">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Trạng thái thanh toán</label>
                        <select name="trang_thai_thanh_toan" class="form-control form-control-sm">
                            <option value="">Tất cả</option>
                            <option value="da_thanh_toan"
                                {{ request('trang_thai_thanh_toan') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán
                            </option>
                            <option value="chua_thanh_toan"
                                {{ request('trang_thai_thanh_toan') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh
                                toán</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Từ ngày</label>
                        <input type="date" name="date_from" value="{{ request('date_from') ?? '' }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Đến ngày</label>
                        <input type="date" name="date_to" value="{{ request('date_to') ?? '' }}"
                            class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fa fa-search"></i> Tìm kiếm
                        </button>
                        @if (request('search') || request('trang_thai_thanh_toan') || request('date_from') || request('date_to'))
                            <a href="{{ route('hoadonsuco.index') }}" class="btn btn-sm btn-light">
                                <i class="fa fa-times"></i> Xóa lọc
                            </a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- 🟢 Thông báo --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fa fa-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- 📋 Bảng danh sách --}}
            {{-- 📋 Bảng danh sách (đồng bộ giao diện sinh viên) --}}
            {{-- 📋 Bảng danh sách (đồng bộ UI, giữ nguyên chức năng gốc) --}}
            <div class="room-table-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 room-table text-center align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Sinh viên</th>
                                <th>Phòng / Khu</th>
                                <th>Ngày gửi</th>
                                <th>Hoàn thành</th>
                                <th>Mô tả</th>
                                <th>Trạng thái</th>
                                <th>Số tiền</th>
                                <th>Thanh toán</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hoa_dons as $hd)
                                @php
                                    $badge = match ($hd->trang_thai) {
                                        'Tiếp nhận' => 'badge-soft-secondary',
                                        'Đang xử lý' => 'badge-soft-warning',
                                        'Hoàn thành' => 'badge-soft-success',
                                        default => 'badge-soft-secondary',
                                    };
                                    $student = $hd->sinhVien ?? null;
                                    $phong = null;
                                    if ($student) {
                                        if ($student->slot && $student->slot->phong) {
                                            $phong = $student->slot->phong;
                                        } elseif ($student->phong) {
                                            $phong = $student->phong;
                                        } elseif ($hd->phong) {
                                            $phong = $hd->phong;
                                        }
                                    } elseif ($hd->phong) {
                                        $phong = $hd->phong;
                                    }
                                    $tenPhongDisplay = $phong->ten_phong ?? '-';
                                    $tenKhuDisplay = $phong->khu->ten_khu ?? '-';
                                @endphp

                                <tr>
                                    <td>{{ $hd->id }}</td>
                                    <td class="text-start">
                                        <div class="fw-semibold">{{ $student->ho_ten ?? '---' }}</div>
                                        <small class="text-muted">MSSV: {{ $student->ma_sinh_vien ?? '---' }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $tenPhongDisplay }}</div>
                                        <small class="text-muted">Khu {{ $tenKhuDisplay }}</small>
                                    </td>
                                    <td>{{ $hd->ngay_gui ? \Carbon\Carbon::parse($hd->ngay_gui)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td>{{ $hd->ngay_hoan_thanh ? \Carbon\Carbon::parse($hd->ngay_hoan_thanh)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="text-start" style="max-width:220px;">
                                        <div class="text-truncate" title="{{ $hd->mo_ta }}">
                                            {{ Str::limit($hd->mo_ta, 50) }}
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $badge }}">{{ $hd->trang_thai }}</span></td>
                                    <td><strong class="text-primary">{{ number_format($hd->payment_amount, 0, ',', '.') }}
                                            ₫</strong></td>
                                    <td>
                                        @if ($hd->is_paid)
                                            <span class="badge badge-soft-success"><i class="fa fa-check-circle"></i> Đã
                                                TT</span>
                                        @else
                                            <span class="badge badge-soft-warning text-dark"><i class="fa fa-clock-o"></i>
                                                Chưa TT</span>
                                        @endif
                                    </td>

                                    {{-- 🔹 Cột hành động --}}
                                    <td class="text-end">
                                        <div class="room-actions">
                                            {{-- Xem chi tiết --}}
                                            <a href="{{ route('suco.show', $hd->id) }}"
                                                class="btn btn-dergin btn-dergin--muted" title="Xem chi tiết">
                                                <i class="fa fa-eye"></i><span>Xem</span>
                                            </a>

                                            {{-- Chưa thanh toán --}}
                                            @if (!$hd->is_paid)
                                                {{-- Mở modal thanh toán --}}
                                                <button type="button" class="btn btn-dergin btn-dergin--info"
                                                    data-bs-toggle="modal" data-bs-target="#paymentModal"
                                                    data-id="{{ $hd->id }}"
                                                    data-amount="{{ $hd->payment_amount }}">
                                                    <i class="fa fa-money"></i><span>Thanh toán</span>
                                                </button>

                                                {{-- Xác nhận nhanh (admin/nhân viên) --}}
                                                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'nhanvien')
                                                    <form action="{{ route('hoadonsuco.xacnhan', $hd->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Xác nhận thanh toán {{ number_format($hd->payment_amount, 0, ',', '.') }} ₫?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-dergin btn-dergin--success">
                                                            <i class="fa fa-check"></i><span>Xác nhận</span>
                                                        </button>
                                                    </form>
                                                @endif

                                                {{-- Đã thanh toán --}}
                                            @else
                                                @if (auth()->user()->role === 'admin')
                                                    <form action="{{ route('hoadonsuco.huy', $hd->id) }}" method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn hủy xác nhận thanh toán?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-dergin btn-dergin--danger">
                                                            <i class="fa fa-undo"></i><span>Hủy TT</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2"
                                            alt="">
                                        <div>Chưa có hóa đơn sự cố nào</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CSS đồng bộ + fix form trong flex --}}
            @push('styles')
                <style>
                    .room-table-wrapper {
                        background: #fff;
                        border-radius: 14px;
                        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                        padding: 1.25rem;
                    }

                    .room-table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0 12px;
                    }

                    .room-table thead th {
                        font-size: .78rem;
                        text-transform: uppercase;
                        letter-spacing: .05em;
                        color: #6c757d;
                        border: none;
                        padding-bottom: .75rem;
                    }

                    .room-table tbody tr {
                        background: #f9fafc;
                        border-radius: 16px;
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .room-table tbody tr:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                    }

                    .room-table tbody td {
                        border: none;
                        vertical-align: middle;
                        padding: 1rem .95rem;
                    }

                    .room-table tbody tr td:first-child {
                        border-top-left-radius: 16px;
                        border-bottom-left-radius: 16px;
                    }

                    .room-table tbody tr td:last-child {
                        border-top-right-radius: 16px;
                        border-bottom-right-radius: 16px;
                    }

                    .room-actions {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: .4rem;
                        flex-wrap: wrap;
                    }

                    .room-actions form {
                        display: contents !important;
                        /* ✅ giúp nút trong flex vẫn submit được */
                    }

                    .btn-dergin {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: .35rem;
                        padding: .4rem .9rem;
                        border-radius: 999px;
                        font-weight: 600;
                        font-size: .72rem;
                        border: none;
                        color: #fff;
                        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                        box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .btn-dergin:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                        color: #fff;
                    }

                    .btn-dergin--muted {
                        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
                    }

                    .btn-dergin--info {
                        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
                    }

                    .btn-dergin--success {
                        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%)
                    }

                    .btn-dergin--danger {
                        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
                    }

                    .badge-soft-success {
                        background: rgba(34, 197, 94, .15);
                        color: #16a34a;
                    }

                    .badge-soft-warning {
                        background: rgba(251, 191, 36, .15);
                        color: #ca8a04;
                    }

                    .badge-soft-secondary {
                        background: rgba(107, 114, 128, .15);
                        color: #374151;
                    }

                    .text-truncate {
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        display: block;
                    }
                </style>
            @endpush

            @push('styles')
                <style>
                    .room-table-wrapper {
                        background: #fff;
                        border-radius: 14px;
                        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                        padding: 1.25rem;
                    }

                    .room-table {
                        margin-bottom: 0;
                        border-collapse: separate;
                        border-spacing: 0 12px;
                    }

                    .room-table thead th {
                        font-size: .78rem;
                        text-transform: uppercase;
                        letter-spacing: .05em;
                        color: #6c757d;
                        border: none;
                        padding-bottom: .75rem;
                    }

                    .room-table tbody tr {
                        background: #f9fafc;
                        border-radius: 16px;
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .room-table tbody tr:hover {
                        transform: translateY(-2px);
                        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                    }

                    .room-table tbody td {
                        border: none;
                        vertical-align: middle;
                        padding: 1rem .95rem;
                    }

                    .room-table tbody tr td:first-child {
                        border-top-left-radius: 16px;
                        border-bottom-left-radius: 16px;
                    }

                    .room-table tbody tr td:last-child {
                        border-top-right-radius: 16px;
                        border-bottom-right-radius: 16px;
                    }

                    .room-actions {
                        display: flex;
                        flex-wrap: nowrap;
                        justify-content: center;
                        gap: .4rem;
                        white-space: nowrap;
                    }

                    .btn-dergin {
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        gap: .35rem;
                        padding: .4rem .9rem;
                        border-radius: 999px;
                        font-weight: 600;
                        font-size: .72rem;
                        border: none;
                        color: #fff;
                        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                        box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                        transition: transform .2s ease, box-shadow .2s ease;
                    }

                    .btn-dergin:hover {
                        transform: translateY(-1px);
                        box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                        color: #fff;
                    }

                    .btn-dergin--muted {
                        background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
                    }

                    .btn-dergin--info {
                        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
                    }

                    .btn-dergin--success {
                        background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%)
                    }

                    .btn-dergin--danger {
                        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
                    }

                    .badge-soft-success {
                        background: rgba(34, 197, 94, .15);
                        color: #16a34a;
                    }

                    .badge-soft-warning {
                        background: rgba(251, 191, 36, .15);
                        color: #ca8a04;
                    }

                    .badge-soft-secondary {
                        background: rgba(107, 114, 128, .15);
                        color: #374151;
                    }
                </style>
            @endpush


            <div class="d-flex justify-content-center mt-3">
                {{ $hoa_dons->onEachSide(1)->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

    {{-- Modal thanh toán --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Thanh toán hóa đơn sự cố</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Số tiền cần thanh toán:</strong>
                        <span id="paymentAmount" class="text-danger fs-5">0 ₫</span>
                    </div>

                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Chọn phương thức thanh toán</label>
                        <select id="paymentMethod" class="form-select" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="tien_mat">💵 Tiền mặt</option>
                            <option value="chuyen_khoan">🏦 Chuyển khoản</option>
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
                                    class="img-fluid rounded border" style="max-width: 120px;">
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="ghi_chu_thanh_toan" class="form-label">Ghi chú thanh toán</label>
                        <textarea name="ghi_chu_thanh_toan" id="ghi_chu_thanh_toan" class="form-control" rows="3"
                            placeholder="Vui lòng nhập thông tin thanh toán (ví dụ: tên phòng-khu, số điện thoại...)" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn">
                        <i class="fa fa-check"></i> Xác nhận thanh toán
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- CSRF token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const paymentMethodSelect = document.getElementById('paymentMethod');
            const bankInfo = document.getElementById('bankInfo');
            const confirmBtn = document.getElementById('confirmPaymentBtn');
            const paymentModal = document.getElementById('paymentModal');
            const paymentAmount = document.getElementById('paymentAmount');

            // Hiển thị thông tin chuyển khoản nếu chọn "chuyen_khoan"
            function toggleBankInfo() {
                const method = paymentMethodSelect?.value;
                if (bankInfo) {
                    bankInfo.style.display = method === 'chuyen_khoan' ? 'block' : 'none';
                }
            }

            if (paymentMethodSelect) {
                paymentMethodSelect.addEventListener('change', toggleBankInfo);
            }

            // Gắn ID và số tiền hóa đơn vào modal khi mở
            if (paymentModal) {
                paymentModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const hoaDonId = button?.getAttribute('data-id');
                    const amount = button?.getAttribute('data-amount');

                    if (confirmBtn && hoaDonId) {
                        confirmBtn.setAttribute('data-id', hoaDonId);
                    }

                    if (paymentAmount && amount) {
                        paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
                    }

                    // Reset form
                    if (paymentMethodSelect) paymentMethodSelect.value = '';
                    if (document.getElementById('ghi_chu_thanh_toan')) {
                        document.getElementById('ghi_chu_thanh_toan').value = '';
                    }
                    toggleBankInfo();
                });
            }

            // Gửi yêu cầu thanh toán
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    const hoaDonId = this?.getAttribute('data-id');
                    const hinhThuc = paymentMethodSelect?.value || '';
                    const ghiChu = document.getElementById('ghi_chu_thanh_toan')?.value || '';

                    if (!hoaDonId || !hinhThuc) {
                        alert('⚠️ Vui lòng chọn hình thức thanh toán!');
                        return;
                    }

                    if (!ghiChu.trim()) {
                        alert('⚠️ Vui lòng nhập ghi chú thanh toán!');
                        return;
                    }

                    // Disable button để tránh double submit
                    this.disabled = true;
                    this.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';

                    fetch(`/admin/hoadonsuco/${hoaDonId}/thanh-toan`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                hinh_thuc_thanh_toan: hinhThuc,
                                ghi_chu_thanh_toan: ghiChu
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert('✅ ' + data.message);
                                const modalInstance = bootstrap.Modal.getInstance(paymentModal);
                                if (modalInstance) modalInstance.hide();
                                setTimeout(() => location.reload(), 500);
                            } else {
                                alert('❌ ' + (data.message || 'Có lỗi xảy ra!'));
                                this.disabled = false;
                                this.innerHTML = '<i class="fa fa-check"></i> Xác nhận thanh toán';
                            }
                        })
                        .catch(err => {
                            console.error('Lỗi gửi yêu cầu:', err);
                            alert('❌ Không thể gửi yêu cầu. Vui lòng thử lại!');
                            this.disabled = false;
                            this.innerHTML = '<i class="fa fa-check"></i> Xác nhận thanh toán';
                        });
                });
            }
        });
    </script>

    <style>
        .table th,
        .table td {
            vertical-align: middle !important;
            font-size: 13px;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 10px;
            font-size: 11px;
        }

        .desc-truncate {
            max-width: 220px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: normal;
            word-break: break-word;
            line-height: 1.3;
            color: #333;
        }

        .text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .card {
            border-radius: 8px;
        }

        .card-body h3 {
            font-weight: 600;
        }
    </style>
@endsection
