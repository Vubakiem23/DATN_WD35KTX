@extends('admin.layouts.admin')

@section('title', 'Sửa thông báo')

@section('content')
<div class="container-fluid">

    <h3 class="mb-3">Chỉnh sửa thông báo</h3>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Form chỉnh sửa --}}
    <form action="{{ route('thongbao.update', $thongbao->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="tieu_de" class="form-label">Tiêu đề</label>
            <input type="text" name="tieu_de" class="form-control" value="{{ old('tieu_de', $thongbao->tieu_de) }}" required>
        </div>

        <div class="mb-3">
            <label for="noi_dung" class="form-label">Nội dung</label>
            <textarea name="noi_dung" class="form-control" rows="5" required>{{ old('noi_dung', $thongbao->noi_dung) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="ngay_dang" class="form-label">Ngày đăng</label>
            <input type="date" name="ngay_dang" class="form-control" value="{{ old('ngay_dang', $thongbao->ngay_dang) }}" required>
        </div>

        <div class="mb-3">
            <label for="doi_tuong" class="form-label">Đối tượng</label>
            <select name="doi_tuong" class="form-select" required>
                <option value="Sinh viên" {{ $thongbao->doi_tuong == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                <option value="Giảng viên" {{ $thongbao->doi_tuong == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                <option value="Tất cả" {{ $thongbao->doi_tuong == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('thongbao.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
