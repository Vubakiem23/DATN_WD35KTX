@extends('client.layouts.app')

@section('title', 'Thông tin cá nhân - Sinh viên')

@push('styles')
<style>
    .profile-page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .profile-page-header h2 {
        margin: 0;
        font-weight: 600;
        font-size: 28px;
    }

    .profile-page-header i {
        font-size: 32px;
        opacity: 0.9;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        margin-bottom: 25px;
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    .profile-card-header {
        padding: 20px 25px;
        border-bottom: none;
        font-weight: 600;
        font-size: 18px;
    }

    .profile-card-header.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .profile-card-header.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .profile-card-header.success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: white;
    }

    .profile-card-body {
        padding: 30px;
    }

    .info-item {
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item:hover {
        background-color: #f8f9fa;
        margin: 0 -15px;
        padding-left: 15px;
        padding-right: 15px;
        border-radius: 8px;
    }

    .info-item strong {
        color: #495057;
        font-weight: 600;
        min-width: 150px;
        display: inline-block;
    }

    .info-item p {
        margin: 0;
        color: #6c757d;
        display: inline;
    }

    .profile-avatar-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        margin-bottom: 25px;
    }

    .profile-avatar-wrapper {
        position: relative;
        padding: 30px;
        text-align: center;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    .profile-avatar {
        width: 250px;
        height: 250px;
        border-radius: 15px;
        object-fit: cover;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
    }

    .profile-avatar-placeholder {
        width: 250px;
        height: 250px;
        border-radius: 15px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .profile-avatar-placeholder i {
        font-size: 120px;
        color: #dee2e6;
    }

    .status-badge {
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
    }

    .section-divider {
        height: 2px;
        background: linear-gradient(90deg, transparent, #dee2e6, transparent);
        border: none;
        margin: 25px 0;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }

    .violation-table {
        border-radius: 10px;
        overflow: hidden;
    }

    .violation-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .violation-table thead th {
        border: none;
        padding: 15px;
        font-weight: 600;
    }

    .violation-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .violation-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    .violation-table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 80px;
        color: #dee2e6;
        margin-bottom: 20px;
    }

    .empty-state h4 {
        color: #6c757d;
        margin-bottom: 15px;
    }

    .empty-state p {
        color: #adb5bd;
    }

    @media (max-width: 768px) {
        .profile-page-header {
            padding: 20px;
        }

        .profile-page-header h2 {
            font-size: 22px;
        }

        .profile-card-body {
            padding: 20px;
        }

        .info-item strong {
            min-width: auto;
            display: block;
            margin-bottom: 5px;
        }

        .profile-avatar {
            width: 200px;
            height: 200px;
        }

        .profile-avatar-placeholder {
            width: 200px;
            height: 200px;
        }
    }
</style>
@endpush

@section('content')
<div class="profile-page-header">
    <h2>
        <i class="fas fa-user me-3"></i>
        Thông tin cá nhân
    </h2>
</div>

@if(!$sinhVien)
<!-- Thông báo chưa nộp hồ sơ -->
<div class="row">
    <div class="col-12">
        <div class="profile-card">
            <div class="profile-card-body">
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h4>Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                    <p>Vui lòng nộp hồ sơ để xem thông tin cá nhân của bạn.</p>
                </div>
            </div>
        </div>
        <div class="profile-card mt-4">
            <div class="profile-card-header info">
                <h5 class="mb-0">
                    <i class="fas fa-user-circle me-2"></i>
                    Thông tin tài khoản
                </h5>
            </div>
            <div class="profile-card-body">
                <div class="info-item">
                    <strong>Họ tên:</strong>
                    <p>{{ $user->name ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <strong>Email:</strong>
                    <p>{{ $user->email ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="row">
    <div class="col-md-8">
        <div class="profile-card">
            <div class="profile-card-header primary">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Chi tiết thông tin
                </h5>
            </div>
            <div class="profile-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Mã sinh viên:</strong>
                            <p>{{ $sinhVien->ma_sinh_vien }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Họ tên:</strong>
                            <p>{{ $sinhVien->ho_ten }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngày sinh:</strong>
                            <p>{{ $sinhVien->ngay_sinh ? $sinhVien->ngay_sinh->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Giới tính:</strong>
                            <p>{{ $sinhVien->gioi_tinh }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Quê quán:</strong>
                            <p>{{ $sinhVien->que_quan }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Nơi ở hiện tại:</strong>
                            <p>{{ $sinhVien->noi_o_hien_tai }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Lớp:</strong>
                            <p>{{ $sinhVien->lop }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngành:</strong>
                            <p>{{ $sinhVien->nganh }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Khóa học:</strong>
                            <p>{{ $sinhVien->khoa_hoc }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Số điện thoại:</strong>
                            <p>{{ $sinhVien->so_dien_thoai }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Email:</strong>
                            <p>{{ $sinhVien->email }}</p>
                        </div>
                    </div>
                </div>
                
                {{-- Phần ảnh --}}
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-images me-2"></i>
                    Hình ảnh
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Ảnh sinh viên:</strong>
                            @if($sinhVien->anh_sinh_vien)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $sinhVien->anh_sinh_vien) }}" 
                                         alt="Ảnh sinh viên" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                </div>
                            @else
                                <p class="text-muted">Chưa có ảnh</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Ảnh giấy xác nhận:</strong>
                            @if($sinhVien->anh_giay_xac_nhan)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $sinhVien->anh_giay_xac_nhan) }}" 
                                         alt="Ảnh giấy xác nhận" 
                                         class="img-thumbnail" 
                                         style="max-width: 200px; max-height: 200px; object-fit: cover;">
                                </div>
                            @else
                                <p class="text-muted">Chưa có ảnh</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($sinhVien->citizen_id_number)
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-id-card me-2"></i>
                    Thông tin CMND/CCCD
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Số CMND/CCCD:</strong>
                            <p>{{ $sinhVien->citizen_id_number }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Ngày cấp:</strong>
                            <p>{{ $sinhVien->citizen_issue_date ? $sinhVien->citizen_issue_date->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Nơi cấp:</strong>
                            <p>{{ $sinhVien->citizen_issue_place }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($sinhVien->guardian_name)
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-users me-2"></i>
                    Thông tin người giám hộ
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Họ tên:</strong>
                            <p>{{ $sinhVien->guardian_name }}</p>
                        </div>
                        <div class="info-item">
                            <strong>Số điện thoại:</strong>
                            <p>{{ $sinhVien->guardian_phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item">
                            <strong>Quan hệ:</strong>
                            <p>{{ $sinhVien->guardian_relationship }}</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <hr class="section-divider">
                <h6 class="section-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Vi phạm của sinh viên
                </h6>
                @if($violations->count())
                <div class="table-responsive">
                    <table class="table violation-table">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại vi phạm</th>
                                <th>Trạng thái</th>
                                <th>Tiền phạt</th>
                                <th>Thanh toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($violations as $vp)
                            @php
                                $status = strtolower((string)$vp->status);
                                $processed = in_array($status, ['resolved','paid'], true);
                                $amountValue = (float) ($vp->penalty_amount ?? 0);
                                $amountLabel = number_format($amountValue, 0, ',', '.') . ' đ';
                            @endphp
                            <tr>
                                <td>{{ optional($vp->occurred_at)->format('d/m/Y') ?? 'N/A' }}</td>
                                <td>{{ $vp->type->name ?? 'Không rõ' }}</td>
                                <td>
                                    <span class="badge bg-{{ $processed ? 'success' : 'warning' }}">
                                        {{ $processed ? 'Đã xử lý' : 'Chưa xử lý' }}
                                    </span>
                                </td>
                                <td>{{ $amountLabel }}</td>
                                <td>
                                    @if(!$processed)
                                        <button type="button"
                                            class="btn btn-sm btn-primary violation-pay-btn"
                                            data-violation-id="{{ $vp->id }}"
                                            data-violation-type="{{ $vp->type->name ?? 'Không rõ' }}"
                                            data-violation-date="{{ optional($vp->occurred_at)->format('d/m/Y') ?? 'N/A' }}"
                                            data-violation-amount-label="{{ $amountLabel }}"
                                            data-violation-amount="{{ $amountValue }}"
                                            data-payment-url="{{ route('client.violations.pay', $vp->id) }}">
                                            <i class="fa fa-money-bill-wave me-1"></i> Thanh toán
                                        </button>
                                    @else
                                        <div class="text-success small fw-semibold">
                                            <i class="fa fa-check-circle me-1"></i>
                                            Đã thanh toán {{ optional($vp->client_paid_at)->format('d/m/Y H:i') ?? '' }}
                                        </div>
                                        <div class="text-muted small">
                                            Hình thức: {{ $vp->client_payment_method === 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt' }}
                                        </div>
                                        @if($vp->client_transfer_image_path)
                                            <a class="small d-inline-flex align-items-center gap-1 mt-1"
                                               href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($vp->client_transfer_image_path) }}"
                                               target="_blank">
                                                <i class="fa fa-image"></i> Xem ảnh chuyển khoản
                                            </a>
                                        @endif
                                        @if($vp->client_payment_note)
                                            <div class="text-muted small fst-italic mt-1">
                                                "{{ $vp->client_payment_note }}"
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted fst-italic text-center py-3">
                        <i class="fas fa-check-circle me-2"></i>
                        Chưa có vi phạm nào.
                    </p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="profile-avatar-card">
            <div class="profile-card-header info">
                <h5 class="mb-0">
                    <i class="fas fa-image me-2"></i>
                    Ảnh đại diện
                </h5>
            </div>
            <div class="profile-avatar-wrapper">
                @if($sinhVien->anh_sinh_vien)
                    <img src="{{ asset('storage/' . $sinhVien->anh_sinh_vien) }}" 
                         alt="Ảnh sinh viên" 
                         class="profile-avatar">
                @else
                    <div class="profile-avatar-placeholder">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <p class="text-muted mt-3 mb-0">Chưa có ảnh</p>
                @endif
            </div>
        </div>
        
        <div class="profile-card">
            <div class="profile-card-header success">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>
                    Trạng thái hồ sơ
                </h5>
            </div>
            <div class="profile-card-body text-center">
                @php
                    $status = $sinhVien->trang_thai_ho_so ?? \App\Models\SinhVien::STATUS_PENDING_APPROVAL;
                    $badgeClass = match ($status) {
                        \App\Models\SinhVien::STATUS_APPROVED => 'bg-success',
                        \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION => 'bg-info',
                        default => 'bg-warning text-dark',
                    };
                @endphp
                <p class="mb-3">
                    <strong class="d-block mb-2">Trạng thái:</strong>
                    <span class="status-badge {{ $badgeClass }} text-white">
                        {{ $status }}
                    </span>
                </p>
                @if ($status === \App\Models\SinhVien::STATUS_PENDING_CONFIRMATION)
                    <a href="{{ route('client.confirmation.show') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-clipboard-check me-1"></i> Xác nhận hồ sơ ngay
                    </a>
                @elseif ($status !== \App\Models\SinhVien::STATUS_APPROVED)
                    <p class="text-muted small mb-0">Hồ sơ đang chờ ban quản lý duyệt.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal thanh toán vi phạm -->
<div class="modal fade" id="violationPaymentModal" tabindex="-1" aria-labelledby="violationPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="violationPaymentModalLabel">
                    <i class="fa fa-money-bill-wave me-2"></i>Thanh toán vi phạm
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3 bg-light rounded p-3">
                    <p class="mb-1"><strong>Loại vi phạm:</strong> <span data-field="violation-type">-</span></p>
                    <p class="mb-1"><strong>Ngày vi phạm:</strong> <span data-field="violation-date">-</span></p>
                    <p class="mb-0"><strong>Tiền phạt:</strong> <span data-field="violation-amount">0 đ</span></p>
                </div>
                <div class="mb-3">
                    <label for="violationPaymentMethod" class="form-label">Hình thức thanh toán</label>
                    <select id="violationPaymentMethod" class="form-select" required>
                        <option value="">-- Chọn hình thức --</option>
                        <option value="tien_mat">Tiền mặt</option>
                        <option value="chuyen_khoan">Chuyển khoản</option>
                    </select>
                </div>
                <div class="mb-3" id="violationCashNoteWrapper">
                    <label for="violationPaymentNote" class="form-label">Ghi chú</label>
                    <textarea id="violationPaymentNote" class="form-control" rows="3" placeholder="VD: Thanh toán trực tiếp cho quản lý..."></textarea>
                </div>
                <div class="mb-3 d-none" id="violationTransferSection">
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                            <div>
                                <p class="mb-1"><strong>Tên tài khoản:</strong> Nguyen Quang Thắng</p>
                                <p class="mb-1"><strong>Số tài khoản:</strong> T1209666</p>
                                <p class="mb-1"><strong>Ngân hàng:</strong> Techcombank · CN Hà Nội</p>
                                <p class="text-muted small mb-0">Nội dung chuyển khoản ghi rõ họ tên + phòng + khu.</p>
                            </div>
                            <div class="text-center">
                                <img src="{{ asset('images/maqr.jpg') }}" alt="QR thanh toán" class="img-fluid rounded" style="max-width: 160px;">
                                <div class="small text-muted mt-2">Quét mã để chuyển khoản nhanh</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="violationTransferNote" class="form-label">Ghi chú chuyển khoản</label>
                        <textarea id="violationTransferNote" class="form-control" rows="2" placeholder="Ví dụ: Nguyen Van A - P501 KTX A"></textarea>
                    </div>
                    <div class="mt-3">
                        <label for="violationTransferImage" class="form-label">Ảnh chứng từ chuyển khoản</label>
                        <input type="file" class="form-control" id="violationTransferImage" accept="image/*">
                        <small class="text-muted d-block mt-1">Định dạng JPG, PNG · tối đa 4MB.</small>
                        <div class="mt-2 d-none" id="violationTransferPreview">
                            <img src="" alt="Ảnh chuyển khoản" class="img-fluid rounded shadow-sm">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-success" id="confirmViolationPaymentBtn">
                    <i class="fa fa-paper-plane me-1"></i> Gửi xác nhận
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('violationPaymentModal');
    if (!modalEl) {
        return;
    }
    const modalInstance = window.bootstrap?.Modal ? new bootstrap.Modal(modalEl) : null;
    const methodSelect = document.getElementById('violationPaymentMethod');
    const cashNoteWrapper = document.getElementById('violationCashNoteWrapper');
    const noteInput = document.getElementById('violationPaymentNote');
    const transferSection = document.getElementById('violationTransferSection');
    const transferNote = document.getElementById('violationTransferNote');
    const transferImageInput = document.getElementById('violationTransferImage');
    const transferPreview = document.getElementById('violationTransferPreview');
    const transferPreviewImg = transferPreview ? transferPreview.querySelector('img') : null;
    const confirmBtn = document.getElementById('confirmViolationPaymentBtn');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let currentSubmitUrl = null;

    const fieldMap = {
        type: modalEl.querySelector('[data-field="violation-type"]'),
        date: modalEl.querySelector('[data-field="violation-date"]'),
        amount: modalEl.querySelector('[data-field="violation-amount"]'),
    };

    function resetModalState() {
        currentSubmitUrl = null;
        if (methodSelect) {
            methodSelect.value = '';
        }
        if (noteInput) {
            noteInput.value = '';
        }
        if (transferNote) {
            transferNote.value = '';
        }
        if (transferImageInput) {
            transferImageInput.value = '';
        }
        if (transferPreview) {
            transferPreview.classList.add('d-none');
        }
        if (transferPreviewImg) {
            transferPreviewImg.src = '';
        }
        toggleTransferSection('');
    }

    function toggleTransferSection(method) {
        if (!transferSection || !cashNoteWrapper) {
            return;
        }
        if (method === 'chuyen_khoan') {
            transferSection.classList.remove('d-none');
            cashNoteWrapper.classList.add('d-none');
        } else {
            transferSection.classList.add('d-none');
            cashNoteWrapper.classList.remove('d-none');
        }
    }

    document.querySelectorAll('.violation-pay-btn').forEach((button) => {
        button.addEventListener('click', () => {
            resetModalState();
            currentSubmitUrl = button.getAttribute('data-payment-url');
            if (fieldMap.type) {
                fieldMap.type.textContent = button.getAttribute('data-violation-type') || '-';
            }
            if (fieldMap.date) {
                fieldMap.date.textContent = button.getAttribute('data-violation-date') || '-';
            }
            if (fieldMap.amount) {
                fieldMap.amount.textContent = button.getAttribute('data-violation-amount-label') || '0 đ';
            }
            if (modalInstance) {
                modalInstance.show();
            } else {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
            }
        });
    });

    methodSelect?.addEventListener('change', function (event) {
        toggleTransferSection(event.target.value);
    });

    transferImageInput?.addEventListener('change', function (event) {
        const file = event.target.files?.[0];
        if (!file || !transferPreview || !transferPreviewImg) {
            transferPreview?.classList.add('d-none');
            if (transferPreviewImg) {
                transferPreviewImg.src = '';
            }
            return;
        }
        transferPreviewImg.src = URL.createObjectURL(file);
        transferPreview.classList.remove('d-none');
    });

    confirmBtn?.addEventListener('click', function () {
        if (!currentSubmitUrl) {
            alert('Không xác định được vi phạm cần thanh toán.');
            return;
        }
        const methodValue = methodSelect?.value;
        if (!methodValue) {
            alert('Vui lòng chọn hình thức thanh toán.');
            return;
        }
        const formData = new FormData();
        formData.append('hinh_thuc_thanh_toan', methodValue);
        const noteValue = methodValue === 'chuyen_khoan'
            ? (transferNote?.value || '')
            : (noteInput?.value || '');
        formData.append('ghi_chu', noteValue);

        if (methodValue === 'chuyen_khoan') {
            const file = transferImageInput?.files?.[0] || null;
            if (file) {
                formData.append('anh_chuyen_khoan', file);
            }
        }

        fetch(currentSubmitUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: formData,
        })
            .then(async (response) => {
                const text = await response.text();
                let data = null;
                if (text) {
                    try {
                        data = JSON.parse(text);
                    } catch (error) {
                        console.error('[ViolationPaymentModal]', 'Không thể parse JSON', text);
                    }
                }
                if (!response.ok || !data || !data.success) {
                    throw new Error(data?.message || text || 'Thanh toán thất bại');
                }
                return data;
            })
            .then(() => {
                alert('✅ Đã ghi nhận thanh toán vi phạm!');
                if (modalInstance) {
                    modalInstance.hide();
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
                window.location.reload();
            })
            .catch((error) => {
                console.error('[ViolationPaymentModal]', error);
                alert('❌ ' + (error?.message || 'Không thể gửi yêu cầu thanh toán.'));
            });
    });
});
</script>
@endpush
