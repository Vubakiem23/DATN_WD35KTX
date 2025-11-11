@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">

            <div>
                <h3 class="room-page__title mb-1">Vi phạm sinh viên</h3>
                <p class="text-muted mb-0">Ghi nhận, lọc và xử lý các vi phạm</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('vipham.create') }}" class="btn btn-dergin btn-dergin--info"><i class="fa fa-plus"></i><span>Ghi vi phạm</span></a>
            </div>

        @push('styles')
        <style>
            .room-page__title{font-size:1.75rem;font-weight:700;color:#1f2937}
            .room-table-wrapper{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:1.25rem}
            .room-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
            .room-table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;border:none;padding-bottom:.75rem}
            .room-table tbody tr{background:#f9fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
            .room-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(15,23,42,0.08)}
            .room-table tbody td{border:none;vertical-align:middle;padding:1rem .95rem}
            .room-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
            .room-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
            .room-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:.4rem}
            .room-actions .btn-dergin{min-width:80px}
            .room-actions .btn-dergin span{line-height:1;white-space:normal}
            .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
            .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
            .btn-dergin i{font-size:.8rem}
            .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
            .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
            .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
            /* Giảm tràn ngang trên màn hình rộng vừa */
            @media (max-width:1400px){
                .room-actions .btn-dergin{min-width:72px;padding:.35rem .7rem}
            }
            @media (max-width:992px){
                .room-table thead{display:none}
                .room-table tbody{display:block}
                .room-table tbody tr{display:flex;flex-direction:column;padding:1rem}
                .room-table tbody td{display:flex;justify-content:space-between;padding:.35rem 0}
                .room-actions{justify-content:flex-start}
            }
        </style>
        @endpush

        <form method="GET" class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-3">
                        <label class="small text-muted mb-1">Tìm ghi chú/biên lai</label>
                        <input type="text" name="q" value="{{ $q }}" class="form-control"
                            placeholder="Ví dụ: BL-2025-001">
                    </div>
                    <div class="form-group col-md-3">
                        <label class="small text-muted mb-1">Tên/Mã sinh viên</label>
                        <input type="text" name="student_keyword" value="{{ $studentKeyword ?? '' }}"
                            class="form-control" placeholder="VD: Nguyễn Văn A hoặc PD05...">
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small text-muted mb-1">Loại</label>
                        <select name="type_id" class="form-control">
                            <option value="">-- Tất cả --</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected($typeId == $t->id)>{{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="small text-muted mb-1">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="">-- Tất cả --</option>
                            <option value="open" @selected($status == 'open')>Chưa xử lý</option>
                            <option value="resolved" @selected($status == 'resolved')>Đã xử lý</option>
                        </select>
                    </div>
                    <div class="form-group col-md-1">
                        <label class="small text-muted mb-1">Từ ngày</label>
                        <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                    </div>
                    <div class="form-group col-md-1">
                        <label class="small text-muted mb-1">Đến ngày</label>
                        <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary mr-2"><i class="fa fa-filter mr-1"></i> Lọc</button>
                    <a href="{{ route('vipham.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                </div>
                <div></div>
            </div>


        </form>


        <div class="room-table-wrapper">
            <div class="table-responsive">
                <table class="table table-hover mb-0 room-table">
                        <thead>
                            <tr>
                                <th class="fit">Thời điểm</th>
                                <th>Sinh viên</th>
                                <th>Mã sinh viên</th>
                                <th>Hình ảnh(Nếu có)</th>
                                <th>Loại</th>
                                <th class="fit">Trạng thái</th>
                                <th class="text-end fit">Tiền phạt</th>
                                <th class="fit">Biên lai</th>
                                <!-- <th>Ghi chú</th> -->
                                <th class="text-end fit">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($violations as $v)
                                @php
                                    $isResolved = $v->status === 'resolved'; // true = đã xử lý
                                    $statusText = $isResolved ? 'Đã xử lý' : 'Chưa xử lý';
                                    // giữ lớp badge bạn đang dùng
                                    $statusBadge = $isResolved ? 'badge-soft-success' : 'badge-soft-warning';
                                    $noteShort = \Illuminate\Support\Str::limit($v->note, 50);
                                @endphp
                                <tr>
                                    <td class="fit">{{ $v->occurred_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <div class="font-weight-600">{{ $v->student->ho_ten ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">{{ $v->student->ma_sinh_vien ?? '' }}</div>
                                    </td>
                                    <td>
                                        @if ($v->image)
                                            <img src="{{ asset('storage/' . $v->image) }}" alt="Ảnh vi phạm" width="80"
                                                class="rounded">
                                        @else
                                            <span class="text-muted">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $v->type->name ?? 'N/A' }}</td>
                                    <td class="fit">
                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-end fit">{{ $v->penalty_amount ? number_format($v->penalty_amount, 0, ',', '.') : '-' }}</td>
                                    <td class="fit">{{ $v->receipt_no ?? '-' }}</td>
                                    <!-- <td>
                                        @if ($v->note)
                                            <span data-toggle="tooltip"
                                                title="{{ $v->note }}">{{ $noteShort }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td> -->
                                    <td class="text-end fit">
                                        <div class="room-actions">
                                            <a href="{{ route('vipham.show', $v->id) }}" class="btn btn-dergin btn-dergin--muted" title="Xem chi tiết"><i class="fa fa-eye"></i><span>Chi tiết</span></a>
                                            <a href="{{ route('vipham.edit', $v->id) }}" class="btn btn-dergin" title="Sửa"><i class="fa fa-pencil"></i><span>Sửa</span></a>
                                            @if ($v->status == 'open')
                                            <form action="{{ route('vipham.resolve', $v->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-dergin btn-dergin--info" title="Đánh dấu đã xử lý"><i class="fa fa-check"></i><span>Xử lý</span></button>
                                            </form>
                                            @endif
                                            <form action="{{ route('vipham.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa vi phạm này?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-dergin btn-dergin--danger" title="Xóa"><i class="fa fa-trash"></i><span>Xóa</span></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" alt=""
                                            class="mb-2">
                                        <div>Chưa có dữ liệu vi phạm</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Hiển thị {{ $violations->firstItem() ?? 0 }}–{{ $violations->lastItem() ?? 0 }} /
                {{ $violations->total() }} bản ghi
            </div>
            <div>
                {{ $violations->links() }}
            </div>
        </div>


        <div class="mt-3 d-flex justify-content-center">
            {{ $violations->links() }}
        </div>
    </div>
@endsection
