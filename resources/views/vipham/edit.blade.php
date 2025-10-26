@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0">✏️ Sửa vi phạm</h3>
                <small class="text-muted">Cập nhật thông tin biên bản</small>
            </div>
            <a href="{{ route('vipham.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>

        <<form method="POST" action="{{ route('vipham.update', $violation->id) }}" enctype="multipart/form-data"
            class="card shadow-sm border-0">
            >
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label class="small text-muted mb-1">Sinh viên</label>
                        <select name="sinh_vien_id" class="form-control" required>
                            @foreach ($students as $st)
                                <option value="{{ $st->id }}" @selected($violation->sinh_vien_id == $st->id)>
                                    {{ $st->ho_ten }} ({{ $st->ma_sinh_vien }})
                                </option>
                            @endforeach
                        </select>
                        @error('sinh_vien_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label class="small text-muted mb-1">Loại vi phạm</label>
                        <select name="violation_type_id" class="form-control" required>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected($violation->violation_type_id == $t->id)>{{ $t->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('violation_type_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh (nếu có)</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="small text-muted mb-1">Ngày xảy ra</label>
                        <input type="date" name="occurred_at"
                            value="{{ old('occurred_at', optional($violation->occurred_at)->format('Y-m-d')) }}"
                            class="form-control" required>
                        @error('occurred_at')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label class="small text-muted mb-1">Trạng thái</label>
                        <select name="status" class="form-control" required>
                            <option value="open" @selected($violation->status == 'open')>Chưa xử lý</option>
                            <option value="resolved" @selected($violation->status == 'resolved')>Đã xử lý</option>
                        </select>
                        @error('status')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-4">
                        <label class="small text-muted mb-1">Tiền phạt (nếu có)</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="penalty_amount"
                                value="{{ old('penalty_amount', $violation->penalty_amount) }}" class="form-control">
                            <div class="input-group-append"><span class="input-group-text">VND</span></div>
                        </div>
                        @error('penalty_amount')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="small text-muted mb-1">Số biên lai</label>
                        <input type="text" name="receipt_no" value="{{ old('receipt_no', $violation->receipt_no) }}"
                            class="form-control">
                        @error('receipt_no')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label class="small text-muted mb-1">Ghi chú</label>
                        <textarea name="note" rows="3" class="form-control">{{ old('note', $violation->note) }}</textarea>
                        @error('note')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-end">
                <button class="btn btn-success"><i class="fa fa-save mr-1"></i> Cập nhật</button>
            </div>
            </form>
    </div>
@endsection

@push('styles')
    <style>
        .card .form-control,
        .card .form-select {
            height: 40px;
        }
    </style>
@endpush
