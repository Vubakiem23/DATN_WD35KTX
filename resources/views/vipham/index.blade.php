@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <h3>📒 Vi phạm sinh viên</h3>

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
                <div>
                    <a href="{{ route('vipham.create') }}" class="btn btn-primary">
                        <i class="fa fa-plus mr-1"></i> Ghi vi phạm
                    </a>
                </div>
            </div>


        </form>


        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-violations">
                        <thead>
                            <tr>
                                <th class="fit">Thời điểm</th>
                                <th>Sinh viên</th>
                                <th>Mã sinh viên</th>
                                <th>Hình ảnh(Nếu có)</th>
                                <th>Loại</th>
                                <th class="fit">Trạng thái</th>
                                <th class="text-right fit">Tiền phạt</th>
                                <th class="fit">Biên lai</th>
                                <th>Ghi chú</th>
                                <th class="text-right fit">Thao tác</th>
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
                                    <td class="text-right fit">
                                        {{ $v->penalty_amount ? number_format($v->penalty_amount, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="fit">{{ $v->receipt_no ?? '-' }}</td>
                                    <td>
                                        @if ($v->note)
                                            <span data-toggle="tooltip"
                                                title="{{ $v->note }}">{{ $noteShort }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-right fit">
                                        <div class="btn-group">
                                            <a href="{{ route('vipham.show', $v->id) }}"
                                                class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                                <i class="fa fa-eye"></i>
                                            </a>

                                            <a href="{{ route('vipham.edit', $v->id) }}"
                                                class="btn btn-sm btn-outline-primary" title="Sửa">
                                                <i class="fa fa-pencil"></i>
                                            </a>

                                            @if ($v->status == 'open')
                                                <form action="{{ route('vipham.resolve', $v->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button class="btn btn-sm btn-outline-success"
                                                        title="Đánh dấu đã xử lý">
                                                        <i class="fa fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <form action="{{ route('vipham.destroy', $v->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Xóa vi phạm này?')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                                    <i class="fa fa-trash"></i>
                                                </button>
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
