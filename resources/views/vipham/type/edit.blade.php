@extends('admin.layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h3 class="mb-0">✏️ Sửa loại vi phạm</h3>
                <small class="text-muted">Cập nhật tên/diễn giải loại vi phạm</small>
            </div>
            <a href="{{ route('loaivipham.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left mr-1"></i> Quay lại
            </a>
        </div>

        <form method="POST" action="{{ route('loaivipham.update', $type->id) }}" class="card shadow-sm border-0">
            @csrf @method('PUT')
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label class="small text-muted mb-1">Code (mã viết tắt)</label>
                        <input type="text" name="code" value="{{ old('code', $type->code) }}" class="form-control"
                            required>
                        @error('code')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group col-md-8">
                        <label class="small text-muted mb-1">Tên loại vi phạm</label>
                        <input type="text" name="name" value="{{ old('name', $type->name) }}" class="form-control"
                            required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="small text-muted mb-1">Mô tả</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $type->description) }}</textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
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
