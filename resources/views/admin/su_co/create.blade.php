@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Thêm sự cố mới</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('suco.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ✅ Chọn sinh viên --}}
            <div class="form-group mb-3">
                <label for="sinh_vien_id"><strong>Sinh viên</strong></label>
                <select name="sinh_vien_id" id="sinh_vien_id" class="form-control" required>
                    <option value="">-- Chọn sinh viên --</option>
                    @foreach($sinhviens as $sv)
                        <option value="{{ $sv->id }}">
                            {{ $sv->ho_ten }} (MSSV: {{ $sv->ma_sinh_vien }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Chọn phòng --}}
            <div class="form-group mb-3">
                <label for="phong_id"><strong>Phòng</strong></label>
                <select name="phong_id" id="phong_id" class="form-control" required>
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $p)
                        <option value="{{ $p->id }}">{{ $p->ten_phong }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Mô tả sự cố --}}
            <div class="form-group mb-3">
                <label for="mo_ta"><strong>Mô tả sự cố</strong></label>
                <textarea name="mo_ta" id="mo_ta" rows="4" class="form-control" placeholder="Nhập mô tả chi tiết..." required></textarea>
            </div>

            {{-- Upload ảnh --}}
            <div class="form-group mb-4">
                <label for="anh"><strong>Ảnh minh chứng (tùy chọn)</strong></label>
                <input type="file" name="anh" id="anh" class="form-control" accept="image/*" onchange="previewImage(event)">
                <small class="form-text text-muted">Chọn ảnh JPG, PNG (tối đa 2MB)</small>

                {{-- Hiển thị ảnh xem trước --}}
                <div class="mt-3">
                    <img id="preview" src="{{ asset('images/no-image.png') }}" 
                         alt="Xem trước ảnh" 
                         width="200" 
                         style="border-radius: 10px; object-fit: cover; display: none; border: 1px solid #ccc;">
                </div>
            </div>

            <div class="form-group text-end">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Lưu sự cố
                </button>
                <a href="{{ route('suco.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Preview ảnh --}}
<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = "{{ asset('images/no-image.png') }}";
        preview.style.display = 'none';
    }
}
</script>

{{-- CSS --}}
<style>
.form-group label {
    font-weight: 600;
}
.btn i {
    margin-right: 4px;
}
</style>
@endsection
