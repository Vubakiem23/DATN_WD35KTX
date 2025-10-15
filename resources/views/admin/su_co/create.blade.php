@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Thêm sự cố mới</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        <form action="{{ route('suco.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Sinh viên</label>
                <select name="sinh_vien_id" class="form-control" required>
                    <option value="">-- Chọn sinh viên --</option>
                    @foreach($sinhviens as $sv)
                        <option value="{{ $sv->id }}">{{ $sv->ten }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Phòng</label>
                <select name="phong_id" class="form-control" required>
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $p)
                        <option value="{{ $p->id }}">{{ $p->ten_phong }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Mô tả sự cố</label>
                <textarea name="mo_ta" rows="4" class="form-control" placeholder="Nhập mô tả chi tiết..." required></textarea>
            </div>

            <button type="submit" class="btn btn-success">Lưu sự cố</button>
            <a href="{{ route('suco.index') }}" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>
</div>
@endsection
