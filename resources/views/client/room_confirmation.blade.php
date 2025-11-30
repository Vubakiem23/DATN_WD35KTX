@extends('client.layouts.app')

@section('title', 'Xác nhận vào phòng')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-door-open me-2"></i>
                        Xác nhận vào phòng
                    </h4>
                </div>
                <div class="card-body">
                    @if($assignment && $assignment->phong)
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="fas fa-info-circle me-2"></i>
                                Thông báo quan trọng
                            </h5>
                            <p class="mb-0">
                                Bạn đã được gán vào phòng <strong>{{ $assignment->phong->ten_phong }}</strong> 
                                ({{ $assignment->phong->khu->ten_khu ?? 'N/A' }}). 
                                Vui lòng xác nhận và thanh toán tiền phòng để hoàn tất quá trình vào phòng.
                            </p>
                        </div>

                        <!-- Thông tin phòng và thanh toán -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="info-card-header">
                                        <i class="fas fa-home me-2"></i>
                                        <span>Thông tin phòng</span>
                                    </div>
                                    <div class="info-card-body">
                                        <div class="info-item">
                                            <span class="info-label">Tên phòng</span>
                                            <span class="info-value">{{ $assignment->phong->ten_phong }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Khu</span>
                                            <span class="info-value">{{ $assignment->phong->khu->ten_khu ?? 'N/A' }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Sức chứa</span>
                                            <span class="info-value">{{ $assignment->phong->suc_chua }} người</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">Ngày bắt đầu</span>
                                            <span class="info-value">{{ $assignment->start_date ? \Carbon\Carbon::parse($assignment->start_date)->format('d/m/Y') : 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card h-100">
                                    <div class="info-card-header">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        <span>Thông tin thanh toán</span>
                                    </div>
                                    <div class="info-card-body">
                                        @if($hoaDon && $slotPayment)
                                            @php
                                                // Tính số tiền của slot này
                                                $tienPhongSlot = (int) ($hoaDon->tien_phong_slot ?? 0);
                                                $slotBillingCount = (int) ($hoaDon->slot_billing_count ?? 1);
                                                
                                                if ($slotBillingCount > 0 && $tienPhongSlot > 0) {
                                                    // Chia đều tiền phòng cho số slot
                                                    $tienMoiSlot = (int) ($tienPhongSlot / $slotBillingCount);
                                                } else {
                                                    // Fallback: tính từ phòng nếu không có dữ liệu hóa đơn
                                                    if ($hoaDon->phong) {
                                                        $tienMoiSlot = (int) ($hoaDon->phong->giaSlot() ?? 0);
                                                    } else {
                                                        $tienMoiSlot = 0;
                                                    }
                                                }
                                            @endphp
                                            <div class="info-item">
                                                <span class="info-label">Hóa đơn tháng</span>
                                                <span class="info-value">{{ $hoaDon->thang }}</span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Số tiền</span>
                                                <span class="info-value">
                                                    <span class="amount-badge">
                                                        {{ number_format($tienMoiSlot, 0, ',', '.') }} đ
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="info-item">
                                                <span class="info-label">Trạng thái</span>
                                                <span class="info-value">
                                                    @if($slotPayment->da_thanh_toan)
                                                        <span class="status-badge status-paid">Đã thanh toán</span>
                                                    @elseif($slotPayment->trang_thai === \App\Models\HoaDonSlotPayment::TRANG_THAI_CHO_XAC_NHAN)
                                                        <span class="status-badge status-pending">Chờ xác nhận</span>
                                                    @else
                                                        <span class="status-badge status-unpaid">Chưa thanh toán</span>
                                                    @endif
                                                </span>
                                            </div>
                                        @else
                                            <div class="info-item">
                                                <span class="text-muted">Đang tải thông tin thanh toán...</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form thanh toán -->
                        @if($slotPayment && !$slotPayment->da_thanh_toan)
                            <div class="card shadow-sm">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-credit-card me-2"></i>
                                        Thanh toán tiền phòng
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('client.room.confirm') }}" method="POST" enctype="multipart/form-data" id="paymentForm">
                                        @csrf
                                        
                                        <!-- Hình thức thanh toán -->
                                        <div class="mb-4">
                                            <label for="hinh_thuc_thanh_toan" class="form-label fw-bold">
                                                Hình thức thanh toán <span class="text-danger">*</span>
                                            </label>
                                            <select class="form-select" id="hinh_thuc_thanh_toan" name="hinh_thuc_thanh_toan" required>
                                                <option value="">-- Chọn hình thức --</option>
                                                <option value="tien_mat" {{ old('hinh_thuc_thanh_toan') == 'tien_mat' ? 'selected' : '' }}>
                                                    Tiền mặt
                                                </option>
                                                <option value="chuyen_khoan" {{ old('hinh_thuc_thanh_toan') == 'chuyen_khoan' ? 'selected' : '' }}>
                                                    Chuyển khoản
                                                </option>
                                            </select>
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <strong>Tiền mặt:</strong> Thanh toán trực tiếp tại văn phòng. Bạn sẽ được gán vào phòng ngay sau khi thanh toán.<br>
                                                <strong>Chuyển khoản:</strong> Chuyển khoản qua ngân hàng. Vui lòng đợi ban quản lý xác nhận sau khi chuyển khoản.
                                            </small>
                                            @error('hinh_thuc_thanh_toan')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Thông tin chuyển khoản (chỉ hiện khi chọn chuyển khoản) -->
                                        <div id="transferInfoSection" class="mb-4 d-none">
                                            <div class="border rounded p-3 bg-light">
                                                <h6 class="mb-3">
                                                    <i class="fas fa-university me-2 text-primary"></i>
                                                    Thông tin chuyển khoản
                                                </h6>
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3">
                                                    <div>
                                                        <p class="mb-2"><strong>Tên tài khoản:</strong> Nguyen Quang Thắng</p>
                                                        <p class="mb-2"><strong>Số tài khoản:</strong> T1209666</p>
                                                        <p class="mb-2"><strong>Ngân hàng thụ hưởng:</strong> Techcombank · Chi nhánh Hà Nội</p>
                                                        <p class="text-muted small mb-0">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            Ghi rõ phòng và khu trong nội dung chuyển khoản.
                                                        </p>
                                                    </div>
                                                    <div class="text-center ms-md-3">
                                                        @if(file_exists(public_path('images/maqr.jpg')))
                                                            <img src="{{ asset('images/maqr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded shadow-sm" style="max-width: 160px;">
                                                            <div class="small text-muted mt-2">Quét mã để chuyển khoản</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Upload ảnh chuyển khoản (chỉ hiện khi chọn chuyển khoản) -->
                                        <div id="transferImageSection" class="mb-3 d-none">
                                            <label for="anh_chuyen_khoan" class="form-label">
                                                Ảnh chứng từ chuyển khoản
                                            </label>
                                            <input type="file" class="form-control" id="anh_chuyen_khoan" 
                                                   name="anh_chuyen_khoan" accept="image/*">
                                            <small class="form-text text-muted">
                                                Định dạng JPG, PNG · Tối đa 4MB
                                            </small>
                                            <div class="mt-2 d-none" id="transferImagePreview">
                                                <img src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                                            </div>
                                            @error('anh_chuyen_khoan')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Ghi chú chung -->
                                        <div class="mb-4">
                                            <label for="ghi_chu" class="form-label">
                                                Ghi chú
                                            </label>
                                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" 
                                                      rows="3" placeholder="Nhập ghi chú (nếu có)...">{{ old('ghi_chu') }}</textarea>
                                            @error('ghi_chu')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Buttons -->
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button type="button" class="btn btn-danger me-auto" onclick="rejectRoom()">
                                                <i class="fas fa-times-circle me-2"></i>
                                                Từ chối phòng
                                            </button>
                                            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                Quay lại
                                            </a>
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Xác nhận và thanh toán
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <!-- Form ẩn để từ chối -->
                                    <form action="{{ route('client.room.reject') }}" method="POST" id="rejectForm" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @elseif($slotPayment && $slotPayment->da_thanh_toan)
                            <div class="alert alert-success">
                                <h5 class="alert-heading">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Đã thanh toán
                                </h5>
                                <p class="mb-0">
                                    Bạn đã thanh toán tiền phòng thành công. 
                                    @if($slotPayment->ngay_thanh_toan)
                                        Ngày thanh toán: {{ \Carbon\Carbon::parse($slotPayment->ngay_thanh_toan)->format('d/m/Y H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-center">
                                <a href="{{ route('client.dashboard') }}" class="btn btn-primary">
                                    <i class="fas fa-home me-2"></i>
                                    Về trang chủ
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h5 class="alert-heading">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Chưa có thông tin thanh toán
                                </h5>
                                <p class="mb-0">
                                    Vui lòng liên hệ ban quản lý để được hỗ trợ.
                                </p>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Lỗi
                            </h5>
                            <p class="mb-0">
                                Không tìm thấy thông tin phòng được gán. Vui lòng liên hệ ban quản lý.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    
    .form-select:focus,
    .form-control:focus {
        border-color: #0066cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }
    
    .btn-danger {
        transition: all 0.3s ease;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }
    
    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }
    
    .info-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    
    .info-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 20px;
        font-weight: 600;
        font-size: 16px;
        display: flex;
        align-items: center;
    }
    
    .info-card-header i {
        font-size: 18px;
    }
    
    .info-card-body {
        padding: 20px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }
    
    .info-value {
        color: #212529;
        font-size: 15px;
        font-weight: 600;
        text-align: right;
    }
    
    .amount-badge {
        display: inline-block;
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #212529;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
    }
    
    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.status-paid {
        background: #d4edda;
        color: #155724;
    }
    
    .status-badge.status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-badge.status-unpaid {
        background: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('hinh_thuc_thanh_toan');
    const transferInfoSection = document.getElementById('transferInfoSection');
    const transferImageSection = document.getElementById('transferImageSection');
    const anhChuyenKhoan = document.getElementById('anh_chuyen_khoan');
    const transferImagePreview = document.getElementById('transferImagePreview');
    
    function toggleTransferSections() {
        const isTransfer = paymentMethodSelect && paymentMethodSelect.value === 'chuyen_khoan';
        
        if (transferInfoSection) {
            transferInfoSection.classList.toggle('d-none', !isTransfer);
        }
        if (transferImageSection) {
            transferImageSection.classList.toggle('d-none', !isTransfer);
        }
        
        if (anhChuyenKhoan) {
            if (isTransfer) {
                anhChuyenKhoan.setAttribute('required', 'required');
            } else {
                anhChuyenKhoan.removeAttribute('required');
            }
        }
    }
    
    if (paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', toggleTransferSections);
        // Trigger on load if value is already set
        toggleTransferSections();
    }
    
    // Preview image when selected
    if (anhChuyenKhoan) {
        anhChuyenKhoan.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = transferImagePreview.querySelector('img');
                    if (img) {
                        img.src = e.target.result;
                        transferImagePreview.classList.remove('d-none');
                    }
                };
                reader.readAsDataURL(file);
            } else {
                transferImagePreview.classList.add('d-none');
            }
        });
    }
    
    // Form validation
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const selectedMethod = paymentMethodSelect ? paymentMethodSelect.value : '';
            if (!selectedMethod) {
                e.preventDefault();
                alert('Vui lòng chọn một trong các tùy chọn sau.');
                paymentMethodSelect.focus();
                return false;
            }
            
            if (selectedMethod === 'chuyen_khoan' && anhChuyenKhoan && !anhChuyenKhoan.files.length) {
                e.preventDefault();
                alert('Vui lòng upload ảnh chứng từ chuyển khoản');
                anhChuyenKhoan.focus();
                return false;
            }
        });
    }
});

function rejectRoom() {
    if (confirm('Bạn có chắc chắn muốn từ chối phòng này? Bạn sẽ phải chờ admin gán phòng khác.')) {
        document.getElementById('rejectForm').submit();
    }
}
</script>
@endsection

