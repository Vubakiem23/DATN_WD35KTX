@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản phòng')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Thêm tài sản / thiết bị phòng</h3>

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
          <ul>
              @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
      </div>
    @endif

    <form action="{{ route('taisan.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tài sản trong kho</label>
                <select name="kho_tai_san_id" class="form-select" required>
                    <option value="">-- Chọn tài sản trong kho --</option>
                    @foreach($khoTaiSans as $kho)
                        <option value="{{ $kho->id }}">
                            {{ $kho->ma_tai_san }} - {{ $kho->ten_tai_san }} (Còn: {{ $kho->so_luong }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Số lượng cấp</label>
                <input type="number" name="so_luong" class="form-control" required min="1">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tình trạng ban đầu</label>
                <input type="text" name="tinh_trang" class="form-control" placeholder="VD: Mới, Cũ, Hư hỏng...">
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Tình trạng hiện tại</label>
                <select name="tinh_trang_hien_tai" class="form-select">
                    <option value="">-- Chọn tình trạng --</option>
                    <option value="Mới">Mới</option>
                    <option value="Cũ">Cũ</option>
                    <option value="Bảo trì">Bảo trì</option>
                    <option value="Đã hỏng">Đã hỏng</option>
                </select>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Phòng</label>
                <select name="phong_id" class="form-select">
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $phong)
                        <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-success">Lưu</button>
            <a href="{{ route('taisan.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection
