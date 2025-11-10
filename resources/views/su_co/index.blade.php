@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title d-flex justify-content-between align-items-center flex-wrap">
        <h2><i class="fa fa-exclamation-circle text-primary"></i> Danh sách sự cố</h2>
        <a href="{{ route('suco.create') }}" class="btn btn-sm btn-primary mt-2 mt-sm-0">
            <i class="fa fa-plus"></i> Thêm sự cố
        </a>
    </div>

    <div class="x_content">
        {{-- 🔍 Tìm kiếm và lọc --}}
        <form method="GET" action="{{ route('suco.index') }}" class="mb-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Tìm kiếm</label>
                    <input type="text" name="search" value="{{ request('search') ?? '' }}"
                           class="form-control form-control-sm"
                           placeholder="MSSV hoặc Họ tên">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Trạng thái</label>
                    <select name="trang_thai" class="form-control form-control-sm">
                        <option value="">Tất cả</option>
                        <option value="Tiếp nhận" {{ request('trang_thai') == 'Tiếp nhận' ? 'selected' : '' }}>Tiếp nhận</option>
                        <option value="Đang xử lý" {{ request('trang_thai') == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="Hoàn thành" {{ request('trang_thai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
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
                    @if(request('search') || request('trang_thai') || request('date_from') || request('date_to'))
                        <a href="{{ route('suco.index') }}" class="btn btn-sm btn-light">
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
                        <th>Ảnh</th>
                        <th class="text-start">Mô tả</th>
                        <th>Trạng thái</th>
                        <th>Giá tiền</th>
                        <th>Thanh toán</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($su_cos as $sc)
                        <tr class="{{ $sc->trang_thai == 'Hoàn thành' ? 'table-success' : '' }}">
                            <td>{{ $sc->id }}</td>
                            <td class="text-start" style="max-width:150px;">
                                <span class="text-truncate d-block" style="font-size:13px;">
                                    {{ $sc->sinhVien->ho_ten ?? '---' }}
                                </span>
                                <small class="text-muted d-block" style="font-size:11px;">MSSV: {{ $sc->sinhVien->ma_sinh_vien ?? '---' }}</small>
                            </td>
                            <td>
                                @php
                                    // Ưu tiên lấy phòng từ slot (nếu có), nếu không thì lấy từ phong_id trực tiếp
                                    $student = $sc->sinhVien ?? null;
                                    $phong = null;
                                    if ($student) {
                                        // Kiểm tra slot và phong của slot
                                        if (isset($student->slot) && $student->slot && isset($student->slot->phong) && $student->slot->phong) {
                                            $phong = $student->slot->phong;
                                        } elseif (isset($student->phong) && $student->phong) {
                                            $phong = $student->phong;
                                        } elseif (isset($sc->phong) && $sc->phong) {
                                            $phong = $sc->phong;
                                        }
                                    } elseif (isset($sc->phong) && $sc->phong) {
                                        $phong = $sc->phong;
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
                            <td>{{ $sc->ngay_gui ? \Carbon\Carbon::parse($sc->ngay_gui)->format('d/m/Y') : '-' }}</td>
                            <td>{{ $sc->ngay_hoan_thanh ? \Carbon\Carbon::parse($sc->ngay_hoan_thanh)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if($sc->anh && file_exists(public_path($sc->anh)))
                                    <img src="{{ asset($sc->anh) }}" class="img-thumbnail shadow-sm" style="width:35px;height:35px;object-fit:cover;">
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td class="text-start">
                                <div class="desc-truncate" title="{{ $sc->mo_ta }}">
                                    {{ $sc->mo_ta }}
                                </div>
                            </td>
                            <td>
                                @php
                                    $badge = match($sc->trang_thai) {
                                        'Tiếp nhận' => 'bg-secondary',
                                        'Đang xử lý' => 'bg-info',
                                        'Hoàn thành' => 'bg-success',
                                        default => 'bg-light text-dark'
                                    };
                                @endphp
                                <span class="badge {{ $badge }}">{{ $sc->trang_thai }}</span>
                            </td>
                            <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount,0,',','.').' ₫' : '0 ₫' }}</td>
                            <td>
                                @if($sc->payment_amount == 0)
                                    <span class="badge bg-secondary">Không TT</span>
                                @elseif($sc->is_paid)
                                    <span class="badge bg-success">Đã TT</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chưa TT</span>
                                @endif
                            </td>
                            <td class="text-end suco-actions">
                                <a href="{{ route('suco.show', $sc->id) }}" class="btn btn-outline-info btn-action" title="Xem">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('suco.edit', $sc->id) }}" class="btn btn-outline-primary btn-action" title="Sửa">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('suco.destroy', $sc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-action" type="submit" title="Xóa"><i class="fa fa-trash"></i></button>
                                </form>
                                @if($sc->trang_thai != 'Hoàn thành')
                                    <button type="button" class="btn btn-success btn-sm mt-1" 
                                            data-bs-toggle="modal" data-bs-target="#hoanThanhModal"
                                            data-id="{{ $sc->id }}"
                                            data-payment="{{ $sc->payment_amount }}"
                                            data-is-paid="{{ $sc->is_paid }}"
                                            data-ngay="{{ $sc->ngay_hoan_thanh }}">
                                        Hoàn thành
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-3">Chưa có sự cố nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $su_cos->onEachSide(1)->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

{{-- Modal Hoàn thành --}}
<div class="modal fade" id="hoanThanhModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✅ Hoàn thành sự cố</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="hoanThanhForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="suco_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> <strong>Lưu ý:</strong> Hoàn thành sự cố chỉ cập nhật trạng thái và ảnh. 
                        Để tạo hóa đơn, vui lòng vào trang chi tiết sự cố.
                    </div>
                    <div class="mb-3">
                        <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành <span class="text-danger">*</span></label>
                        <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="anh_modal" class="form-label">Ảnh minh chứng (sau khi sửa)</label>
                        <input type="file" name="anh" id="anh_modal" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Tải lên ảnh sau khi đã sửa xong sự cố</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('hoanThanhModal');
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const ngay = button.getAttribute('data-ngay');

        document.getElementById('suco_id').value = id;
        document.getElementById('ngay_hoan_thanh').value = ngay || '';

        document.getElementById('hoanThanhForm').action = "{{ route('suco.hoanthanh', ':id') }}".replace(':id', id);
    });
});
</script>

<style>
.table th, .table td { vertical-align: middle !important; font-size: 13px; }
.badge { padding: 4px 8px; border-radius: 10px; font-size: 11px; }
.btn-action { width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; font-size:12px; }
.suco-actions button.btn-sm { font-size:13px; }
.desc-truncate { max-width:220px; overflow:hidden; text-overflow:ellipsis; white-space:normal; word-break:break-word; line-height:1.3; color:#333; }
.text-truncate { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; display:block; }
</style>
@endsection
