@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h2>🛠️ Lên lịch bảo trì tài sản</h2>

    <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 🏠 Chọn phòng --}}
        <div class="form-group mb-3">
            <label for="phong_id">Chọn phòng</label>
            <select id="phong_id" class="form-control">
                <option value="">-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                @endforeach
            </select>
        </div>

        {{-- 💼 Chọn tài sản --}}
        <div class="form-group mb-3">
            <label for="tai_san_id">Chọn tài sản</label>
            <select name="tai_san_id" id="tai_san_id" class="form-control">
                <option value="">-- Chọn tài sản --</option>
                @foreach($taiSan as $ts)
                <option value="{{ $ts->id }}" data-phong="{{ $ts->phong_id }}">
                    {{ $ts->ten_tai_san }}
                    @if($ts->phong)
                    - (Phòng: {{ $ts->phong->ten_phong }})
                    @else
                    - (Chưa gán phòng)
                    @endif
                </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="hinh_anh">Hình ảnh:</label>
            <input type="file" name="hinh_anh" class="form-control">
        </div>
        {{-- 📅 Ngày bảo trì --}}
        <div class="form-group mb-3">
            <label for="ngay_bao_tri">Ngày bảo trì</label>
            <input type="date" name="ngay_bao_tri" id="ngay_bao_tri" class="form-control" required>
        </div>

        {{-- ✏️ Mô tả --}}
        <div class="form-group mb-3">
            <label for="mo_ta">Mô tả</label>
            <textarea name="mo_ta" id="mo_ta" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success"> Lưu lịch bảo trì</button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary"> Quay lại</a>
    </form>
</div>

{{-- 🧠 Script lọc tài sản --}}
<script>
    document.getElementById('phong_id').addEventListener('change', function() {
        var phongId = this.value;
        var allOptions = document.querySelectorAll('#tai_san_id option');

        allOptions.forEach(opt => {
            if (opt.value === '') {
                opt.style.display = 'block'; // giữ lại dòng "Chọn tài sản"
            } else if (!phongId || opt.dataset.phong === phongId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });

        // Reset lại dropdown tài sản
        document.getElementById('tai_san_id').value = '';
    });
</script>
@endsection