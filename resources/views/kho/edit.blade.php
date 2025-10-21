@extends('admin.layouts.admin')
@section('title', 'Cập nhật tài sản kho')

@section('content')
<div class="container">
  <h4 class="mb-3">Cập nhật tài sản</h4>

  <form action="{{ route('kho.update', $kho->id) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="mb-3">
      <label>Mã tài sản</label>
      <input class="form-control" value="{{ $kho->ma_tai_san }}" readonly>
    </div>

    <div class="mb-3">
      <label>Tên tài sản</label>
      <input name="ten_tai_san" class="form-control" value="{{ $kho->ten_tai_san }}" required>
    </div>

    <div class="mb-3">
      <label>Đơn vị tính</label>
      <input name="don_vi_tinh" class="form-control" value="{{ $kho->don_vi_tinh }}">
    </div>

    <div class="mb-3">
      <label>Số lượng</label>
      <input name="so_luong" type="number" class="form-control" value="{{ $kho->so_luong }}" min="0" required>
    </div>

    <div class="mb-3">
      <label>Hình ảnh</label><br>
      @if($kho->hinh_anh)
        <img src="{{ asset('uploads/kho/'.$kho->hinh_anh) }}" width="100" class="mb-2"><br>
      @endif
      <input type="file" name="hinh_anh" class="form-control">
    </div>

    <div class="mb-3">
      <label>Ghi chú</label>
      <textarea name="ghi_chu" class="form-control">{{ $kho->ghi_chu }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">Cập nhật</button>
    <a href="{{ route('kho.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
