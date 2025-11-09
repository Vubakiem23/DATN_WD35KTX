@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-plus-circle text-primary"></i> Báo cáo sự cố mới</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        {{-- ⚠️ Hiển thị lỗi nếu có --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 📝 Form báo cáo sự cố --}}
        <form action="{{ route('suco.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 🧍 Chọn sinh viên (đã lọc sẵn sinh viên có phòng trong controller) --}}
            <div class="mb-3">
                <label for="sinh_vien_id" class="form-label">Sinh viên</label>
                <select name="sinh_vien_id" id="sinh_vien_id" class="form-control" required>
                    <option value="">-- Chọn sinh viên --</option>
                    @foreach($sinhviens as $sv)
                        <option value="{{ $sv->id }}" data-phong="{{ $sv->phong->ten_phong ?? '' }}" data-phong-id="{{ $sv->phong_id }}">
                            {{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🏠 Phòng (tự động hiển thị theo sinh viên) --}}
            <div class="mb-3">
                <label for="phong_ten" class="form-label">Phòng</label>
                <input type="text" id="phong_ten" class="form-control bg-light" name="phong_ten" 
                       value="Chưa chọn sinh viên" readonly>
                {{-- ẩn id để gửi form --}}
                <input type="hidden" name="phong_id" id="phong_id">
            </div>

            <div class="mb-3">
                <label for="mo_ta" class="form-label">Mô tả sự cố</label>
                <textarea name="mo_ta" class="form-control" rows="4" placeholder="Nhập mô tả chi tiết..." required>{{ old('mo_ta') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="anh" class="form-label">Ảnh minh chứng (nếu có)</label>
                <input type="file" name="anh" class="form-control" accept="image/*">
            </div>

            {{-- 🗓️ Ngày hoàn thành (chỉ hiển thị) --}}
            <div class="mb-3">
                <label class="form-label">📆 Ngày hoàn thành sự cố</label>
                <input type="text" class="form-control bg-light text-muted" 
                       value="--- Chưa hoàn thành ---" readonly>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-paper-plane"></i> Gửi báo cáo
                </button>
                <a href="{{ route('suco.index') }}" class="btn btn-light">Hủy</a>
            </div>
        </form>
    </div>
</div>

{{-- 🧠 Script tự động lấy phòng --}}
<script>
document.getElementById('sinh_vien_id').addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let phongTen = selectedOption.getAttribute('data-phong') || 'Không có';
    let phongId = selectedOption.getAttribute('data-phong-id') || '';

    document.getElementById('phong_ten').value = phongTen;
    document.getElementById('phong_id').value = phongId;
});
</script>

<style>
.x_panel { padding: 20px; border-radius: 8px; }
.form-label { font-weight: 600; }
</style>
@endsection
