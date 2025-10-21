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
    <form action="{{ route('thongbao.update', $thongbao->id) }}" method="POST" enctype="multipart/form-data">
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
                <option value="Sinh viên" {{ old('doi_tuong', $thongbao->doi_tuong) == 'Sinh viên' ? 'selected' : '' }}>Sinh viên</option>
                <option value="Giảng viên" {{ old('doi_tuong', $thongbao->doi_tuong) == 'Giảng viên' ? 'selected' : '' }}>Giảng viên</option>
                <option value="Tất cả" {{ old('doi_tuong', $thongbao->doi_tuong) == 'Tất cả' ? 'selected' : '' }}>Tất cả</option>
            </select>
        </div>

        {{-- Chọn phòng --}}
        <div class="mb-3">
            <label for="phong_id" class="form-label">Chọn phòng (tùy chọn)</label>
            <select name="phong_id" class="form-select" id="phong_id">
                <option value="">-- Không chọn phòng --</option>
                @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}" {{ old('phong_id', $thongbao->phong_id) == $phong->id ? 'selected' : '' }}>
                        {{ $phong->ten_phong }} ({{ $phong->khu }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Chọn khu --}}
        <div class="mb-3">
            <label for="khu" class="form-label">Khu (có thể bỏ trống nếu đã chọn phòng)</label>
            <select name="khu" class="form-select" id="khu">
                <option value="">-- Không chọn khu --</option>
                @foreach($phongs->pluck('khu')->unique() as $khu)
                    <option value="{{ $khu }}" {{ old('khu', $thongbao->phong->khu ?? $thongbao->khu) == $khu ? 'selected' : '' }}>
                        {{ $khu }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">Nếu chọn phòng, khu sẽ tự điền theo phòng.</small>
        </div>

        {{-- Ảnh --}}
        <div class="mb-3">
            <label for="anh" class="form-label">Ảnh thông báo (tùy chọn)</label>
            <input type="file" name="anh" class="form-control" accept="image/*">
            @if($thongbao->anh)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $thongbao->anh) }}" alt="Ảnh hiện tại" width="150">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('thongbao.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('phong_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if(selectedOption.value) {
        const text = selectedOption.text; // ví dụ: "Phòng A (Khu X)"
        const khu = text.match(/\(([^)]+)\)/); // lấy Khu X
        if(khu) {
            document.getElementById('khu').value = khu[1];
        }
    }
});
</script>
@endpush

@endsection
