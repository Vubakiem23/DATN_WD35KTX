@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')


@section('scripts')
<script>
    document.getElementById('kho_tai_san_id').addEventListener('change', function() {
        var selected = this.options[this.selectedIndex];
        var imgUrl = selected.getAttribute('data-image');
        var preview = document.getElementById('preview-image');

        if (imgUrl) {
            preview.src = imgUrl;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
</script>
@endsection








<div class="container mt-4">
    <h4 class="mb-3">🛠️ Thêm tài sản thiết bị phòng</h4>
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif


    </div>
    <form action="{{ route('taisan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Chọn tài sản --}}
        <div class="mb-3">
            <label class="form-label">Chọn tài sản</label>
            <select name="kho_tai_san_id" id="kho_tai_san_id" class="form-select form-control" required>
                <option value="">-- Chọn tài sản --</option>
                @foreach($khoTaiSans as $kho)
                <option value="{{ $kho->id }}" data-image="{{ asset('uploads/' . ($kho->hinh_anh ?? 'default.png')) }}">
                    {{ $kho->ten_tai_san }} (Còn: {{ $kho->so_luong }})
                </option>
                @endforeach
            </select>
        </div>

        {{-- Preview hình ảnh --}}
        <div class="mb-3 text-center">
            <img id="preview-image" src="" alt="Hình ảnh tài sản"
                style="max-width: 250px; border-radius: 8px; display:none; border:1px solid #ddd; padding:4px;">
        </div>

        {{-- Chọn phòng --}}
        <div class="mb-3">
            <label class="form-label">Phòng</label>
            <select name="phong_id" class="form-select form-control" required>
                <option value="" >-- Chọn phòng --</option>
                @foreach($phongs as $phong)
                <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                @endforeach
            </select>
        </div>

        {{-- Tình trạng --}}
        <div class="mb-3">
            <label class="form-label">Tình trạng</label>
            <select name="tinh_trang" class="form-select form-control" required>
                <option value="Bình thường">Bình thường</option>
                <option value="Hỏng">Hỏng</option>
                <option value="Cần bảo trì">Cần bảo trì</option>
            </select>
        </div>

        {{-- Số lượng --}}
        <div class="mb-3">
            <label class="form-label">Số lượng</label>
            <input type="number" name="so_luong" class="form-control" min="1" required placeholder="Nhập số lượng">
        </div>

        
            <button type="submit" class="btn btn-primary"> Lưu tài sản</button>
        </div>
    </form>
</div>
@endsection