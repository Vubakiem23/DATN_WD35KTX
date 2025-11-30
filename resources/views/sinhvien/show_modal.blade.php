<div class="sinhvien-modal-content">
    {{-- Logo/Ảnh sinh viên --}}
    <div class="d-flex justify-content-center mb-4">
        @if (isset($sinhvien->anh_sinh_vien))
            <img src="{{ asset('storage/' . $sinhvien->anh_sinh_vien) }}" alt="{{ $sinhvien->ho_ten }}"
                class="sinhvien-avatar">
        @else
            <div class="sinhvien-avatar-placeholder">
                <span class="sinhvien-avatar-text">{{ strtoupper(substr($sinhvien->ho_ten ?? 'SV', 0, 2)) }}</span>
            </div>
        @endif
    </div>

    {{-- I. Thông tin cá nhân --}}
    <div class="sinhvien-card mb-3">
        <div class="sinhvien-card-header sinhvien-card-header-primary">
            <i class="bi bi-person-fill me-2"></i> Thông tin cá nhân
        </div>
        <div class="sinhvien-card-body">
            <div class="row mb-3">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Mã sinh viên:</span>
                    <span class="sinhvien-value">{{ $sinhvien->ma_sinh_vien }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Họ và tên:</span>
                    <span class="sinhvien-value">{{ $sinhvien->ho_ten }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Ngày sinh:</span>
                    <span class="sinhvien-value">
                        {{ $sinhvien->ngay_sinh ? \Carbon\Carbon::parse($sinhvien->ngay_sinh)->format('d/m/Y') : '-' }}
                    </span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Giới tính:</span>
                    <span class="sinhvien-value">{{ $sinhvien->gioi_tinh ?? '-' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Số CCCD:</span>
                    <span class="sinhvien-value">{{ $sinhvien->citizen_id_number ?? '-' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Trạng thái hồ sơ:</span>
                    @php
                        $status = $sinhvien->trang_thai_ho_so ?? 'Khác';
                        $badge = match ($status) {
                            'Đã duyệt' => 'sinhvien-badge-success',
                            'Chờ duyệt' => 'sinhvien-badge-warning',
                            default => 'sinhvien-badge-secondary',
                        };
                    @endphp
                    <span class="sinhvien-badge {{ $badge }}">{{ $status }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Ngày cấp:</span>
                    <span class="sinhvien-value">
                    {{ $sinhvien->citizen_issue_date ? \Carbon\Carbon::parse($sinhvien->citizen_issue_date)->format('d/m/Y') : '-' }}
                    </span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Nơi cấp:</span>
                    <span class="sinhvien-value">{{ $sinhvien->citizen_issue_place ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- II. Người thân --}}
    <div class="sinhvien-card mb-3">
        <div class="sinhvien-card-header sinhvien-card-header-info">
            <i class="bi bi-people-fill me-2"></i> Thông tin người thân
        </div>
        <div class="sinhvien-card-body">
            <div class="row">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Họ tên:</span>
                    <span class="sinhvien-value">{{ $sinhvien->guardian_name ?? '—' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Số điện thoại:</span>
                    <span class="sinhvien-value">{{ $sinhvien->guardian_phone ?? '—' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Quan hệ:</span>
                    <span class="sinhvien-value">{{ $sinhvien->guardian_relationship ?? '—' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- III. Thông tin học tập & ký túc xá --}}
    <div class="sinhvien-card mb-3">
        <div class="sinhvien-card-header sinhvien-card-header-success">
            <i class="bi bi-building me-2"></i> Thông tin học tập & ký túc xá
        </div>
        <div class="sinhvien-card-body">
            <div class="row mb-3">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Lớp:</span>
                    <span class="sinhvien-value">{{ $sinhvien->lop ?? '-' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Ngành:</span>
                    <span class="sinhvien-value">{{ $sinhvien->nganh ?? '-' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Khóa học:</span>
                    <span class="sinhvien-value">{{ $sinhvien->khoa_hoc ?? '-' }}</span>
                </div>
            </div>
            <div class="row mb-3">
                @php
                    // Ưu tiên lấy phòng từ slot (nếu có), nếu không thì lấy từ phong_id trực tiếp
                    $phongHienTai =
                        $sinhvien->slot && $sinhvien->slot->phong ? $sinhvien->slot->phong : $sinhvien->phong ?? null;
                @endphp
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Phòng:</span>
                    <span class="sinhvien-value">{{ $phongHienTai->ten_phong ?? '—' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Khu:</span>
                    <span class="sinhvien-value">{{ $phongHienTai->khu->ten_khu ?? '—' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Email:</span>
                    <span class="sinhvien-value">{{ $sinhvien->email }}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Số điện thoại:</span>
                    <span class="sinhvien-value">{{ $sinhvien->so_dien_thoai ?? '-' }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Quê quán:</span>
                    <span class="sinhvien-value">{{ $sinhvien->que_quan }}</span>
                </div>
                <div class="col-md-4 sinhvien-info-item">
                    <span class="sinhvien-label">Nơi ở hiện tại:</span>
                    <span class="sinhvien-value">{{ $sinhvien->noi_o_hien_tai }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- IV. Ảnh giấy xác nhận --}}
    @if(isset($sinhvien->anh_giay_xac_nhan))
    <div class="sinhvien-card mb-3">
        <div class="sinhvien-card-header sinhvien-card-header-info">
            <i class="bi bi-file-image me-2"></i> Ảnh giấy xác nhận
        </div>
        <div class="sinhvien-card-body">
            <div class="text-center">
                <button type="button" class="btn btn-primary mb-3" onclick="openImageModal('{{ asset('storage/' . $sinhvien->anh_giay_xac_nhan) }}', 'Ảnh giấy xác nhận')">
                    <i class="bi bi-eye me-2"></i> Xem ảnh giấy xác nhận
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- V. Lịch sử vi phạm --}}
    <div class="sinhvien-card">
        <div class="sinhvien-card-header sinhvien-card-header-danger d-flex justify-content-between align-items-center">
            <span><i class="bi bi-exclamation-triangle-fill me-2"></i> Lịch sử vi phạm</span>
            <div class="sinhvien-card-actions">
                <a href="{{ route('vipham.index', ['student_id' => $sinhvien->id]) }}"
                    class="sinhvien-btn sinhvien-btn-danger me-2">
                    Lịch sử vi phạm
                </a>
                <a href="{{ route('vipham.create', ['student_id' => $sinhvien->id]) }}"
                    class="sinhvien-btn sinhvien-btn-success">
                    + Ghi vi phạm
                </a>
            </div>
        </div>
        <div class="sinhvien-card-body">
            @php
                $violations = $sinhvien->violations->sortByDesc('occurred_at');
            @endphp

            @if ($violations->isEmpty())
                <div class="sinhvien-empty-state">Chưa có vi phạm nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle sinhvien-table">
                        <thead class="table-light">
                            <tr>
                                <th>Thời điểm</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Tiền phạt</th>
                                <th>Biên lai</th>
                                <th>Ghi chú</th>
                                <th>Thanh toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($violations as $v)
                                @php
                                    $statusText = $v->status === 'resolved' ? 'Đã xử lý' : 'Chưa xử lý';
                                    $statusClass = $v->status === 'resolved' ? 'sinhvien-badge-success' : 'sinhvien-badge-warning';
                                @endphp
                                <tr>
                                    <td>{{ $v->occurred_at ? \Carbon\Carbon::parse($v->occurred_at)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>{{ $v->type->name ?? '-' }}</td>
                                    <td><span class="sinhvien-badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td class="text-end">
                                        {{ $v->penalty_amount ? number_format($v->penalty_amount, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>{{ $v->receipt_no ?? '-' }}</td>
                                    <td>{{ $v->note ? \Illuminate\Support\Str::limit($v->note, 60) : '-' }}</td>
                                    <td>
                                        @if($v->client_paid_at)
                                            <span class="badge bg-success">
                                                {{ optional($v->client_paid_at)->format('d/m/Y H:i') }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $v->client_payment_method === 'chuyen_khoan' ? 'Chuyển khoản' : 'Tiền mặt' }}
                                            </div>
                                        @else
                                            <span class="text-muted small">Chưa thanh toán</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    /* Container chính */
    .sinhvien-modal-content {
        padding: 1.5rem;
        background: #f8f9fa;
    }

    /* Avatar */
    .sinhvien-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .sinhvien-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .sinhvien-avatar-text {
        color: #fff;
        font-size: 2rem;
        font-weight: 600;
        letter-spacing: 1px;
    }

    /* Card */
    .sinhvien-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        margin-bottom: 1rem;
    }

    /* Card Header - Đơn giản, không gradient */
    .sinhvien-card-header {
        padding: 0.875rem 1.25rem;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.01em;
        border-bottom: 1px solid #e9ecef;
        background: #f8f9fa;
        color: #495057;
    }

    .sinhvien-card-header-primary {
        background: #f8f9fa;
        color: #495057;
        border-left: 3px solid #6c757d;
    }

    .sinhvien-card-header-info {
        background: #f8f9fa;
        color: #495057;
        border-left: 3px solid #6c757d;
    }

    .sinhvien-card-header-success {
        background: #f8f9fa;
        color: #495057;
        border-left: 3px solid #6c757d;
    }

    .sinhvien-card-header-danger {
        background: #f8f9fa;
        color: #495057;
        border-left: 3px solid #6c757d;
    }

    .sinhvien-card-actions {
        display: flex;
        gap: 0.5rem;
    }

    /* Card Body */
    .sinhvien-card-body {
        padding: 1.25rem;
        background: #fff;
    }

    /* Info Item - Tinh tế hơn */
    .sinhvien-info-item {
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .sinhvien-info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .sinhvien-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #6c757d;
        margin-bottom: 0.35rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sinhvien-value {
        display: block;
        font-size: 0.95rem;
        color: #212529;
        font-weight: 400;
        line-height: 1.5;
    }

    /* Badge - Nhẹ nhàng hơn */
    .sinhvien-badge {
        display: inline-block;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .sinhvien-badge-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .sinhvien-badge-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .sinhvien-badge-secondary {
        background: #e9ecef;
        color: #495057;
        border: 1px solid #dee2e6;
    }

    /* Buttons - Đơn giản hơn */
    .sinhvien-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        border-radius: 4px;
        border: 1px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .sinhvien-btn-success {
        background: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    .sinhvien-btn-success:hover {
        background: #218838;
        border-color: #1e7e34;
        color: #fff;
        text-decoration: none;
    }

    .sinhvien-btn-danger {
        background: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }

    .sinhvien-btn-danger:hover {
        background: #c82333;
        border-color: #bd2130;
        color: #fff;
        text-decoration: none;
    }

    /* Table - Tinh tế hơn */
    .sinhvien-table {
        font-size: 0.875rem;
        margin-bottom: 0;
    }

    .sinhvien-table thead th {
        color: #495057;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
        background: #f8f9fa;
    }

    .sinhvien-table tbody td {
        color: #212529;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .sinhvien-table tbody tr:hover {
        background: #f8f9fa;
    }

    /* Empty State */
    .sinhvien-empty-state {
        text-align: center;
        color: #6c757d;
        padding: 2rem;
        font-size: 0.9rem;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sinhvien-modal-content {
            padding: 1rem;
        }

        .sinhvien-card-header {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start !important;
        }

        .sinhvien-card-actions {
            width: 100%;
            flex-direction: column;
        }

        .sinhvien-btn {
            width: 100%;
        }
    }
</style>

<script>
function openImageModal(imageUrl, title) {
    // Tạo modal động
    var modalHtml = `
        <div class="modal fade" id="imageViewModal" tabindex="-1" role="dialog" aria-labelledby="imageViewModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageViewModalLabel">
                            <i class="bi bi-file-image me-2"></i> ${title}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeImageModal()">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center" style="max-height: 70vh; overflow-y: auto;">
                        <img src="${imageUrl}" 
                             alt="${title}" 
                             class="img-fluid rounded shadow-sm"
                             style="max-width: 100%; max-height: 70vh; object-fit: contain;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeImageModal()">Đóng</button>
                        <a href="${imageUrl}" 
                           target="_blank" 
                           class="btn btn-primary">
                            <i class="bi bi-box-arrow-up-right me-2"></i> Mở trong tab mới
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Xóa modal cũ nếu có
    var oldModal = document.getElementById('imageViewModal');
    if (oldModal) {
        oldModal.remove();
    }
    
    // Thêm modal mới vào body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Hiển thị modal (Bootstrap 4)
    $('#imageViewModal').modal('show');
    
    // Hoặc nếu dùng Bootstrap 5
    // var modal = new bootstrap.Modal(document.getElementById('imageViewModal'));
    // modal.show();
}

function closeImageModal() {
    $('#imageViewModal').modal('hide');
    setTimeout(function() {
        var modal = document.getElementById('imageViewModal');
        if (modal) {
            modal.remove();
        }
    }, 300);
}
</script>
