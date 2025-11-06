<div class="container py-3">

    {{-- Ảnh sinh viên --}}
    @if (isset($sinhvien->anh_sinh_vien))
        <div class="d-flex justify-content-center mb-3">
            <img src="{{ asset('storage/' . $sinhvien->anh_sinh_vien) }}" alt="{{ $sinhvien->ho_ten }}"
                class="rounded-circle shadow" width="180" height="180" style="object-fit: cover;">
        </div>
    @endif

    {{-- I. Thông tin cá nhân --}}
    <div class="card mb-3 border-primary">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-person-fill me-2"></i> Thông tin cá nhân
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-md-4"><strong>Mã sinh viên:</strong> {{ $sinhvien->ma_sinh_vien }}</div>
                <div class="col-md-4"><strong>Họ và tên:</strong> {{ $sinhvien->ho_ten }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Ngày sinh:</strong>
                    {{ $sinhvien->ngay_sinh ? \Carbon\Carbon::parse($sinhvien->ngay_sinh)->format('d/m/Y') : '-' }}
                </div>
                <div class="col-md-4"><strong>Giới tính:</strong> {{ $sinhvien->gioi_tinh ?? '-' }}</div>
                <div class="col-md-4"><strong>Trạng thái hồ sơ:</strong>
                    @php
                        $status = $sinhvien->trang_thai_ho_so ?? 'Khác';
                        $badge = match ($status) {
                            'Đã duyệt' => 'bg-success',
                            'Chờ duyệt' => 'bg-warning',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badge }}">{{ $status }}</span>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Số CCCD:</strong> {{ $sinhvien->citizen_id_number ?? '-' }}</div>
                <div class="col-md-4"><strong>Ngày cấp:</strong>
                    {{ $sinhvien->citizen_issue_date ? \Carbon\Carbon::parse($sinhvien->citizen_issue_date)->format('d/m/Y') : '-' }}
                </div>
                <div class="col-md-4"><strong>Nơi cấp:</strong> {{ $sinhvien->citizen_issue_place ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- II. Người thân --}}
    <div class="card mb-3 border-info">
        <div class="card-header bg-info text-white fw-bold">
            <i class="bi bi-people-fill me-2"></i> Thông tin người thân
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-md-4"><strong>Họ tên:</strong> {{ $sinhvien->guardian_name ?? '—' }}</div>
                <div class="col-md-4"><strong>Số điện thoại:</strong> {{ $sinhvien->guardian_phone ?? '—' }}</div>
                <div class="col-md-4"><strong>Quan hệ:</strong> {{ $sinhvien->guardian_relationship ?? '—' }}</div>
            </div>
        </div>
    </div>

    {{-- III. Thông tin học tập & ký túc xá --}}
    <div class="card mb-3 border-success">
        <div class="card-header bg-success text-white fw-bold">
            <i class="bi bi-building me-2"></i> Thông tin học tập & ký túc xá
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-md-4"><strong>Lớp:</strong> {{ $sinhvien->lop ?? '-' }}</div>
                <div class="col-md-4"><strong>Ngành:</strong> {{ $sinhvien->nganh ?? '-' }}</div>
                <div class="col-md-4"><strong>Khóa học:</strong> {{ $sinhvien->khoa_hoc ?? '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Phòng:</strong> {{ $sinhvien->phong->ten_phong ?? '—' }}</div>
                <div class="col-md-4"><strong>Khu:</strong> {{ $sinhvien->phong->khu->ten_khu ?? '—' }}</div>
                <div class="col-md-4"><strong>Email:</strong> {{ $sinhvien->email }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4"><strong>Số điện thoại:</strong> {{ $sinhvien->so_dien_thoai ?? '-' }}</div>
                <div class="col-md-4"><strong>Quê quán:</strong> {{ $sinhvien->que_quan }}</div>
                <div class="col-md-4"><strong>Nơi ở hiện tại:</strong> {{ $sinhvien->noi_o_hien_tai }}</div>
            </div>
        </div>
    </div>

    {{-- IV. Lịch sử vi phạm --}}
    <div class="card border-danger">
        <div class="card-header bg-danger text-white fw-bold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-exclamation-triangle-fill me-2"></i> Lịch sử vi phạm</span>
            <div>
                <a href="{{ route('vipham.create', ['student_id' => $sinhvien->id]) }}"
                    class="btn btn-sm btn-light text-danger me-2">
                    + Ghi vi phạm
                </a>
                <a href="{{ route('vipham.index', ['student_id' => $sinhvien->id]) }}"
                    class="btn btn-sm btn-outline-light">
                    Xem tất cả
                </a>
            </div>
        </div>
        <div class="card-body p-3">
            @php
                $violations = $sinhvien->violations->sortByDesc('occurred_at');
            @endphp

            @if ($violations->isEmpty())
                <div class="text-muted text-center">Chưa có vi phạm nào.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Thời điểm</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Tiền phạt</th>
                                <th>Biên lai</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($violations as $v)
                                @php
                                    $statusText = $v->status === 'resolved' ? 'Đã xử lý' : 'Chưa xử lý';
                                    $statusClass = $v->status === 'resolved' ? 'bg-success' : 'bg-warning';
                                @endphp
                                <tr>
                                    <td>{{ $v->occurred_at ? \Carbon\Carbon::parse($v->occurred_at)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>{{ $v->type->name ?? '-' }}</td>
                                    <td><span class="badge {{ $statusClass }}">{{ $statusText }}</span></td>
                                    <td class="text-end">
                                        {{ $v->penalty_amount ? number_format($v->penalty_amount, 0, ',', '.') : '-' }}
                                    </td>
                                    <td>{{ $v->receipt_no ?? '-' }}</td>
                                    <td>{{ $v->note ? \Illuminate\Support\Str::limit($v->note, 60) : '-' }}</td>
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
    .card {
        border-radius: 10px;
    }

    .card-header {
        font-size: 1rem;
        letter-spacing: 0.2px;
    }

    img.rounded-circle {
        border: 3px solid #dee2e6;
    }
</style>
