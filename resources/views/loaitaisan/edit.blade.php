@extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa loại tài sản')

@section('content')
<div class="container mt-4">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-3 text-primary fw-semibold">✏️ Chỉnh sửa loại tài sản</h4>

    @if ($errors->any())
      <div class="alert alert-danger rounded-3">
        <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('loaitaisan.update', $loai->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label for="ten_loai" class="form-label fw-semibold text-secondary">Tên loại *</label>
        <input type="text" class="form-control" name="ten_loai" id="ten_loai" value="{{ old('ten_loai', $loai->ten_loai) }}" required>
      </div>

      <div class="mb-3">
        <label for="mo_ta" class="form-label fw-semibold text-secondary">Mô tả</label>
        <textarea class="form-control" name="mo_ta" id="mo_ta" rows="3">{{ old('mo_ta', $loai->mo_ta) }}</textarea>
      </div>

      <div class="mb-3">
        <label for="hinh_anh" class="form-label fw-semibold text-secondary">Hình ảnh</label>
        <input type="file" class="form-control" name="hinh_anh" id="hinh_anh" accept="image/*">
        <div class="mt-2">
          @if ($loai->hinh_anh && file_exists(public_path('uploads/loai/'.$loai->hinh_anh)))
            <img id="preview" src="{{ asset('uploads/loai/'.$loai->hinh_anh) }}" alt="Preview" style="width:120px; height:120px; object-fit:cover; border:1px solid #d1d5db; border-radius:8px;">
          @else
            <img id="preview" src="#" alt="Preview" style="display:none; width:120px; height:120px; object-fit:cover; border:1px solid #d1d5db; border-radius:8px;">
          @endif
        </div>
      </div>

      <button type="submit" class="btn btn-primary">💾 Cập nhật</button>
    </form>
  </div>
</div>

<script>
  document.getElementById('hinh_anh').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('preview');
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    }
  });
</script>
@endsection
