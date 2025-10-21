@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>Cập nhật sự cố #{{ $suco->id }}</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('suco.update', $suco->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Sinh viên --}}
            <div class="form-group mb-3">
                <label><strong>Sinh viên</strong></label>
                <input type="text" class="form-control" value="{{ $suco->sinhVien->ten ?? '---' }}" disabled>
            </div>

            {{-- Phòng --}}
            <div class="form-group mb-3">
                <label><strong>Phòng</strong></label>
                <input type="text" class="form-control" value="{{ $suco->phong->ten_phong ?? '---' }}" disabled>
            </div>

            {{-- Mô tả --}}
            <div class="form-group mb-3">
                <label><strong>Mô tả sự cố</strong></label>
                <textarea class="form-control" rows="4" disabled>{{ $suco->mo_ta }}</textarea>
            </div>

            {{-- Ảnh minh chứng hiện tại --}}
            <div class="form-group mb-3">
                <label><strong>Ảnh minh chứng hiện tại</strong></label><br>
                @if(!empty($suco->anh))
                    <img src="{{ asset('uploads/suco/' . $suco->anh) }}" 
                         alt="Ảnh sự cố" width="200" 
                         style="border-radius: 10px; object-fit: cover; border: 1px solid #ccc;">
                @else
                    <img src="{{ asset('images/no-image.png') }}" 
                         alt="Không có ảnh" width="200" 
                         style="border-radius: 10px; object-fit: cover; border: 1px solid #ccc;">
                @endif
            </div>

            {{-- Cập nhật ảnh mới --}}
            <div class="form-group mb-4">
                <label><strong>Thay ảnh mới </strong></label>
                <input type="file" name="anh" id="anh" class="form-control" accept="image/*" onchange="previewImage(event)">
                <div class="mt-3">
                    <img id="preview" src="" 
                         alt="Xem trước ảnh mới" 
                         width="200" 
                         style="border-radius: 10px; object-fit: cover; display: none; border: 1px solid #ccc;">
                </div>
            </div>

            {{-- Trạng thái --}}
            <div class="form-group mb-3">
                <label><strong>Trạng thái</strong></label>
                <select name="trang_thai" class="form-control" required>
                    <option value="Tiếp nhận" {{ $suco->trang_thai == 'Tiếp nhận' ? 'selected' : '' }}>Tiếp nhận</option>
                    <option value="Đang xử lý" {{ $suco->trang_thai == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="Hoàn thành" {{ $suco->trang_thai == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="Hủy" {{ $suco->trang_thai == 'Hủy' ? 'selected' : '' }}>Hủy</option>
                </select>
            </div>

            <div class="text-end">
                <button type="submit" class="btn btn-success">
                    <i class="fa fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('suco.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Preview ảnh mới --}}
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
        preview.src = '';
        preview.style.display = 'none';
    }
}
</script>

<style>
.form-group label {
    font-weight: 600;
}
.btn i {
    margin-right: 4px;
}
</style>
@endsection
