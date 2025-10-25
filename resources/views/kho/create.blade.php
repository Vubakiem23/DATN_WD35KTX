@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản mới')

@section('content')
<div class="container mt-4">
  <h4>➕ Thêm tài sản vào loại: {{ $loai->ten_loai }}</h4>

  @if ($errors->any())
  <div class="alert alert-danger">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif

  <form action="{{ route('kho.store', $loai->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
      <label>Tên tài sản</label>
      <input type="text" name="ten_tai_san" class="form-control" value="{{ old('ten_tai_san') }}" required>
    </div>

    <div class="mb-3">
      <label>Số lượng</label>
      <input type="number" name="so_luong" class="form-control" value="{{ old('so_luong', 1) }}" required>
    </div>

    <div class="mb-3">
      <label>Đơn vị</label>
      <input type="text" name="don_vi_tinh" class="form-control" value="{{ old('don_vi_tinh') }}">
    </div>

    <div class="mb-3">
      <div class="mb-3">
        <label>Tình trạng</label>
        <select name="tinh_trang" class="form-control">
          <option value="">-- Chọn tình trạng --</option>
          @foreach($tinhTrangOptions as $tt)
          <option value="{{ $tt }}" {{ old('tinh_trang') == $tt ? 'selected' : '' }}>
            {{ $tt }}
          </option>
          @endforeach
        </select>
      </div>

    </div>

    <div class="mb-3">
      <label>Ghi chú</label>
      <textarea name="ghi_chu" class="form-control">{{ old('ghi_chu') }}</textarea>
    </div>

    <div class="mb-3">
      <label>Hình ảnh</label>
      <input type="file" name="hinh_anh" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">💾 Thêm tài sản</button>
    <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-secondary">🔙 Quay lại</a>
  </form>
</div>
@endsection