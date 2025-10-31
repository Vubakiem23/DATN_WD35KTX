@extends('admin.layouts.admin')

@section('title', 'Thêm thông báo')

@section('content')
<div class="container mt-4" style="max-width: 900px; background:#f9f9f9; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1);">
    <h3 class="mb-3 text-primary"> Thêm thông báo mới</h3>
    <hr>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('thongbao.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">

            {{-- Tiêu đề --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Tiêu đề</label>
                <div class="d-flex gap-2 flex-wrap">
                    <select name="tieu_de_id" id="tieu_de_id" class="form-select" style="flex:1 1 auto;">
                        <option value="">-- Chọn tiêu đề --</option>
                        @foreach($tieuDes as $td)
                        <option value="{{ $td->id }}">{{ $td->ten_tieu_de }}</option>
                        @endforeach
                    </select>
                    <button type="button" id="add_title_btn" class="btn btn-primary">+ Thêm</button>
                    <button type="button" id="delete_title_btn" class="btn btn-danger">Xóa</button>
                </div>
                <input type="text" id="input_tieu_de" class="form-control mt-2" style="display:none;" placeholder="Nhập tiêu đề mới và Enter để lưu">
            </div>

            {{-- Mức độ --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Mức độ (tùy chọn)</label>
                <div class="d-flex gap-2 flex-wrap">
                    <select name="muc_do_id" id="muc_do_id" class="form-select" style="flex:1 1 auto;">
                        <option value="">-- Không chọn mức độ --</option>
                        @foreach($mucDos as $md)
                        <option value="{{ $md->id }}" {{ old('muc_do_id') == $md->id ? 'selected' : '' }}>
                            {{ $md->ten_muc_do }}
                        </option>
                        @endforeach
                    </select>
                    <button type="button" id="add_priority_btn" class="btn btn-success">+ Thêm</button>
                    <button type="button" id="delete_priority_btn" class="btn btn-danger">Xóa</button>
                </div>
                <input type="text" id="input_muc_do" class="form-control mt-2" style="display:none;" placeholder="Nhập mức độ mới và Enter để lưu">
            </div>

            {{-- Nội dung --}}
            <div class="col-md-12 mb-3">
                <label class="form-label">Nội dung</label>
                <textarea name="noi_dung" class="form-control" rows="5" required>{{ old('noi_dung') }}</textarea>
            </div>

            {{-- Ngày đăng --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Ngày đăng</label>
                <input type="date" name="ngay_dang" class="form-control" value="{{ old('ngay_dang') }}" required>
            </div>

            {{-- Đối tượng --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Đối tượng</label>
                <select name="doi_tuong" class="form-select" required>
                    <option value="">-- Chọn đối tượng --</option>
                    <option value="Sinh viên" {{ old('doi_tuong') == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                    <option value="Giảng viên" {{ old('doi_tuong') == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                    <option value="Tất cả" {{ old('doi_tuong') == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
                </select>
            </div>

            {{-- Khu --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Chọn khu (có thể chọn nhiều)</label>
                <select name="khu_id[]" id="khu_id" class="form-select" multiple>
                    @foreach($khus as $khu)
                    <option value="{{ $khu->id }}" {{ collect(old('khu_id'))->contains($khu->id) ? 'selected' : '' }}>
                        {{ $khu->ten_khu }}
                    </option>
                    @endforeach
                </select>
                <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều khu.</small>
            </div>

            {{-- Phòng --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Chọn phòng (có thể chọn nhiều)</label>
                <select name="phong_id[]" id="phong_id" class="form-select" multiple>
                    @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}" {{ collect(old('phong_id'))->contains($phong->id) ? 'selected' : '' }}>
                        {{ $phong->ten_phong }} ({{ $phong->khu->ten_khu ?? '' }})
                    </option>
                    @endforeach
                </select>
                <small class="text-muted">Giữ Ctrl (Windows) hoặc Cmd (Mac) để chọn nhiều phòng.</small>
            </div>

            {{-- Ảnh --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Ảnh thông báo</label>
                <input type="file" name="anh" class="form-control" accept="image/*">
            </div>

            {{-- File đính kèm --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">File đính kèm</label>
                <input type="file" name="file" class="form-control" accept=".doc,.docx,.pdf,.xls,.xlsx">
            </div>

        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Lưu thông báo</button>
            <a href="{{ route('thongbao.index') }}" class="btn btn-secondary">Hủy</a>
        </div>
    </form>
</div>
@endsection
