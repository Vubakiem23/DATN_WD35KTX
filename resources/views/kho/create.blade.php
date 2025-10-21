@php use Illuminate\Support\Str; @endphp

@extends('admin.layouts.admin')
@section('title', 'Thêm tài sản vào kho')

@section('content')
<div class="container">
  <h4 class="mb-3">Thêm tài sản vào kho</h4>

  <form action="{{ route('kho.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Mã tài sản tự động, không cần nhập --}}
    <input type="hidden" name="ma_tai_san" value="{{ 'TS' . strtoupper(Str::random(6)) }}">

    <div class="mb-3">
      <label>Tên tài sản</label>
      <input name="ten_tai_san" class="form-control" required>
    </div>

    <div class="mb-3">
      <label>Đơn vị tính</label>
      <input name="don_vi_tinh" class="form-control">
    </div>

    <div class="mb-3">
      <label>Số lượng</label>
      <input name="so_luong" type="number" class="form-control" min="0" required>
    </div>

    <div class="mb-3">
      <label>Hình ảnh</label>
      <input type="file" name="hinh_anh" class="form-control">
    </div>

    <div class="mb-3">
      <label>Ghi chú</label>
      <textarea name="ghi_chu" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">Lưu</button>
    <a href="{{ route('kho.index') }}" class="btn btn-secondary">Hủy</a>
  </form>
</div>
@endsection
