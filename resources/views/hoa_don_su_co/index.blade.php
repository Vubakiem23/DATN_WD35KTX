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
                           class="form-control form-control-sm"
                           placeholder="MSSV hoặc Họ tên">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Trạng thái thanh toán</label>
                    <select name="trang_thai_thanh_toan" class="form-control form-control-sm">
                        <option value="">Tất cả</option>
                        <option value="da_thanh_toan" {{ request('trang_thai_thanh_toan') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="chua_thanh_toan" {{ request('trang_thai_thanh_toan') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
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
                    @if(request('search') || request('trang_thai_thanh_toan') || request('date_from') || request('date_to'))
                        <a href="{{ route('hoadonsuco.index') }}" class="btn btn-sm btn-light">
                            <i class="fa fa-times"></i> Xóa lọc
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- 🟢 Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fa fa-info-circle"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- 📋 Bảng danh sách --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle text-center small mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th class="text-start">Sinh viên</th>
                        <th>Phòng / Khu</th>
                        <th>Ngày gửi</th>
                        <th>Hoàn thành</th>
                        <th class="text-start">Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Số tiền</th>
                        <th>Thanh toán</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hoa_dons as $hd)
                        <tr class="{{ $hd->is_paid ? 'table-success' : ($hd->trang_thai == 'Hoàn thành' ? '' : 'table-warning') }}">
                            <td>{{ $hd->id }}</td>
                            <td class="text-start" style="max-width:150px;">
                                <span class="text-truncate d-block" style="font-size:13px;">
                                    {{ $hd->sinhVien->ho_ten ?? '---' }}
                                </span>
                                <small class="text-muted d-block" style="font-size:11px;">MSSV: {{ $hd->sinhVien->ma_sinh_vien ?? '---' }}</small>
                            </td>
                            <td>
                                @php
                                    $student = $hd->sinhVien ?? null;
                                    $phong = null;
                                    if ($student) {
                                        if (isset($student->slot) && $student->slot && isset($student->slot->phong) && $student->slot->phong) {
                                            $phong = $student->slot->phong;
                                        } elseif (isset($student->phong) && $student->phong) {
                                            $phong = $student->phong;
                                        } elseif (isset($hd->phong) && $hd->phong) {
                                            $phong = $hd->phong;
                                        }
                                    } elseif (isset($hd->phong) && $hd->phong) {
                                        $phong = $hd->phong;
                                    }
                                    $tenPhongDisplay = $phong && isset($phong->ten_phong) ? $phong->ten_phong : null;
                                    $khu = ($phong && isset($phong->khu) && $phong->khu) ? $phong->khu : null;
                                    $tenKhuDisplay = $khu && isset($khu->ten_khu) ? $khu->ten_khu : null;
                                @endphp
                                @if ($tenPhongDisplay)
                                    <div>{{ $tenPhongDisplay }}</div>
                                    @if ($tenKhuDisplay)
                                        <small class="badge badge-soft-secondary" style="font-size:10px;">Khu {{ $tenKhuDisplay }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td>{{ $hd->ngay_gui ? \Carbon\Carbon::parse($hd->ngay_gui)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $hd->ngay_hoan_thanh ? \Carbon\Carbon::parse($hd->ngay_hoan_thanh)->format('d/m/Y') : '-' }}</td>
                            <td class="text-start">
                                <div class="desc-truncate" title="{{ $hd->mo_ta }}">
                                    {{ Str::limit($hd->mo_ta, 50) }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $badge = match($hd->trang_thai) {
                                        'Tiếp nhận' => 'bg-secondary',
                                        'Đang xử lý' => 'bg-info',
                                        'Hoàn thành' => 'bg-success',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $hd->trang_thai }}</span>
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($hd->payment_amount, 0, ',', '.') }} ₫</strong>
                            </td>
                            <td>
                                @if($hd->is_paid)
                                    <span class="badge bg-success">
                                        <i class="fa fa-check-circle"></i> Đã TT
                                    </span>
                                @else
                                    <span class="badge bg-warning text-dark">
                                        <i class="fa fa-clock-o"></i> Chưa TT
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-center flex-wrap">
                                    <a href="{{ route('suco.show', $hd->id) }}" class="btn btn-outline-info btn-sm" title="Xem chi tiết">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    @if(!$hd->is_paid)
                                        {{-- Nút thanh toán với modal --}}
                                        <button type="button" 
                                                class="btn btn-success btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#paymentModal"
                                                data-id="{{ $hd->id }}"
                                                data-amount="{{ $hd->payment_amount }}"
                                                title="Thanh toán hóa đơn">
                                            <i class="fa fa-money"></i> Thanh toán
                                        </button>
                                        {{-- Nút xác nhận nhanh (cho admin/nhân viên) --}}
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'nhanvien')
                                            <form action="{{ route('hoadonsuco.xacnhan', $hd->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Xác nhận thanh toán hóa đơn số tiền {{ number_format($hd->payment_amount, 0, ',', '.') }} ₫?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success btn-sm" title="Xác nhận nhanh">
                                                    <i class="fa fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('hoadonsuco.huy', $hd->id) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Bạn có chắc muốn hủy xác nhận thanh toán?');">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-warning btn-sm" title="Hủy thanh toán">
                                                    <i class="fa fa-undo"></i>
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
                                <i class="fa fa-inbox fa-2x mb-2"></i><br>
                                Chưa có hóa đơn sự cố nào.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

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

                <div id="bankInfo" style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <h6 class="mb-3">Thông tin chuyển khoản:</h6>
                    <div class="row">
                        <div class="col-md-7">
                            <p class="mb-2"><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
                            <p class="mb-2"><strong>Số tài khoản:</strong> T1209666</p>
                            <p class="mb-0"><strong>Ngân hàng:</strong> Techcombank - Chi nhánh Hà Nội</p>
                        </div>
                        <div class="col-md-5 text-center">
                            <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded border" style="max-width: 120px;">
                            <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                        </div>
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label for="ghi_chu_thanh_toan" class="form-label">Ghi chú thanh toán</label>
                    <textarea name="ghi_chu_thanh_toan" 
                              id="ghi_chu_thanh_toan" 
                              class="form-control" 
                              rows="3" 
                              placeholder="Vui lòng nhập thông tin thanh toán (ví dụ: tên phòng-khu, số điện thoại...)" 
                              required></textarea>
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
document.addEventListener('DOMContentLoaded', function () {
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
        paymentModal.addEventListener('show.bs.modal', function (event) {
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
        confirmBtn.addEventListener('click', function () {
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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
.table th, .table td { 
    vertical-align: middle !important; 
    font-size: 13px; 
}
.badge { 
    padding: 4px 8px; 
    border-radius: 10px; 
    font-size: 11px; 
}
.desc-truncate { 
    max-width:220px; 
    overflow:hidden; 
    text-overflow:ellipsis; 
    white-space:normal; 
    word-break:break-word; 
    line-height:1.3; 
    color:#333; 
}
.text-truncate { 
    overflow:hidden; 
    text-overflow:ellipsis; 
    white-space:nowrap; 
    display:block; 
}
.card {
    border-radius: 8px;
}
.card-body h3 {
    font-weight: 600;
}
</style>
@endsection

