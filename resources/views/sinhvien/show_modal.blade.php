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

    {{-- IV. Lịch sử vi phạm --}}
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
    }

    /* Avatar */
    .sinhvien-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .sinhvien-avatar-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #e5e7eb;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .sinhvien-avatar-text {
        color: #fff;
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Card */
    .sinhvien-card {
        border: 1px solid #edf1f7;
        border-radius: 0.5rem;
        background: #fff;
        overflow: hidden;
    }

    /* Card Header */
    .sinhvien-card-header {
        padding: 0.875rem 1.25rem;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.2px;
        border-bottom: 1px solid #edf1f7;
        background: #ffffff;
        color: #334155;
    }

    .sinhvien-card-header-primary {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: #fff;
        border-bottom: none;
    }

    .sinhvien-card-header-info {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: #fff;
        border-bottom: none;
    }

    .sinhvien-card-header-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        border-bottom: none;
    }

    .sinhvien-card-header-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: #fff;
        border-bottom: none;
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

    /* Info Item */
    .sinhvien-info-item {
        margin-bottom: 0.75rem;
    }

    .sinhvien-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .sinhvien-value {
        display: block;
        font-size: 0.95rem;
        color: #0f172a;
        font-weight: 500;
    }

    /* Badge */
    .sinhvien-badge {
        display: inline-block;
        padding: 0.4rem 0.7rem;
        border-radius: 10rem;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .sinhvien-badge-success {
        background: #e8fff3;
        color: #107154;
    }

    .sinhvien-badge-warning {
        background: #fff7e6;
        color: #ad6800;
    }

    .sinhvien-badge-secondary {
        background: #f2f4f7;
        color: #3f4753;
    }

    /* Buttons */
    .sinhvien-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.45rem;
        border: none;
        text-decoration: none;
        transition: all 0.2s;
        cursor: pointer;
    }

    .sinhvien-btn-success {
        background: #10b981;
        color: #fff;
    }

    .sinhvien-btn-success:hover {
        background: #059669;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
    }

    .sinhvien-btn-danger {
        background: #ef4444;
        color: #fff;
    }

    .sinhvien-btn-danger:hover {
        background: #dc2626;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    }

    /* Table */
    .sinhvien-table {
        font-size: 0.875rem;
    }

    .sinhvien-table th {
        color: #334155;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .sinhvien-table td {
        color: #0f172a;
        font-size: 0.875rem;
    }

    /* Empty State */
    .sinhvien-empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 2rem;
        font-size: 0.9rem;
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
