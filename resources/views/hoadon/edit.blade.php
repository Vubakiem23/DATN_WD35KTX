@extends('admin.layouts.admin')

@section('content')
<div class="container py-4">
  <h3>✏️ Sửa hóa đơn phòng {{ $hoaDon->phong->ten_phong }}</h3>

  <form action="{{ route('hoadon.update', $hoaDon->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
  <label for="don_gia_dien" class="form-label">Đơn giá điện (VNĐ/kWh)</label>
  <input type="number" name="don_gia_dien" class="form-control" value="{{ $hoaDon->don_gia_dien }}">
</div>

<div class="mb-3">
  <label for="don_gia_nuoc" class="form-label">Đơn giá nước (VNĐ/m³)</label>
  <input type="number" name="don_gia_nuoc" class="form-control" value="{{ $hoaDon->don_gia_nuoc }}">
</div>


    {{-- Thêm các trường khác nếu cần --}}

    <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
    <a href="{{ route('hoadon.index') }}" class="btn btn-secondary">⬅️ Quay lại</a>
  </form>
</div>
@endsection
