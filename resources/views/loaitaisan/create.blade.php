@extends('admin.layouts.admin')

@section('title', 'Thêm loại tài sản')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">➕ Thêm loại tài sản</h4>

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('loaitaisan.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label for="ten_loai" class="form-label">Tên loại *</label>
      <input type="text" class="form-control" name="ten_loai" id="ten_loai" value="{{ old('ten_loai') }}" required>
    </div>

    <div class="mb-3">
      <label for="mo_ta" class="form-label">Mô tả</label>
      <textarea class="form-control" name="mo_ta" id="mo_ta">{{ old('mo_ta') }}</textarea>
    </div>

    <div class="mb-3">
      <label for="hinh_anh" class="form-label">Hình ảnh</label>
      <input type="file" class="form-control" name="hinh_anh" id="hinh_anh" accept="image/*">
      <div class="mt-2">
        <img id="preview" src="#" alt="Preview" style="display:none; width:100px; height:100px; object-fit:cover; border:1px solid #ddd; border-radius:8px;">
      </div>
    </div>

    <button type="submit" class="btn btn-success">➕ Thêm</button>
  </form>
</div>

<script>
  document.getElementById('hinh_anh').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('preview');
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
  });
</script>
@endsection
