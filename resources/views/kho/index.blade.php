@extends('admin.layouts.admin')
@section('title', 'Kho tài sản')

@section('content')
@push('styles')
<style>
  .page-title{font-size:1.5rem;font-weight:700;color:#1f2937}
  .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
  .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
  .btn-dergin i{font-size:.8rem}
  .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}

  .filter-card {
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 15px 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
  }
  .filter-card label {
    font-weight: 600;
    color: #333;
  }
  .filter-btns .btn {
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .filter-btns i { margin-right: 5px; }
</style>
@endpush

<div class="container mt-4">

    <h3 class="page-title mb-0">📦 Kho tài sản</h3>


  {{-- 🟢 Bộ lọc --}}
  <div class="filter-card mb-4">
    <form method="GET" action="{{ route('kho.index') }}" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-layer-group text-primary"></i> Loại tài sản</label>
        <select name="loai_id" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          @foreach($tatCaLoai as $loai)
            <option value="{{ $loai->id }}" {{ request('loai_id') == $loai->id ? 'selected' : '' }}>
              {{ $loai->ten_loai }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-circle-check text-primary"></i> Tình trạng</label>
        <select name="tinh_trang" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          <option value="Mới" {{ request('tinh_trang') == 'Mới' ? 'selected' : '' }}>Mới</option>
          <option value="Đang sử dụng" {{ request('tinh_trang') == 'Đang sử dụng' ? 'selected' : '' }}>Đang sử dụng</option>
          <option value="Hỏng" {{ request('tinh_trang') == 'Hỏng' ? 'selected' : '' }}>Hỏng</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-search text-primary"></i> Từ khóa</label>
        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Tìm theo tên loại...">
      </div>

      <div class="col-md-3 d-flex gap-2 filter-btns">
        <button type="submit" class="btn btn-success flex-fill">
          <i class="fa fa-filter"></i> Lọc
        </button>
        <a href="{{ route('kho.index') }}" class="btn btn-outline-secondary flex-fill">
          <i class="fa fa-rotate-left"></i> Đặt lại
        </a>
      </div>
    </form>
  </div>

  {{-- 🗂️ Danh sách loại tài sản trong kho --}}
  <div class="row">
    @foreach($loaiTaiSan as $loai)
      <div class="col-md-3 mb-3">
        <div class="card shadow-sm text-center">
          @if($loai->hinh_anh)
            <img src="{{ asset('uploads/loai/'.$loai->hinh_anh) }}" class="card-img-top" style="height:150px;object-fit:cover;">
          @endif
          <div class="card-body">
            <h5 class="card-title">{{ $loai->ten_loai }}</h5>
            <p class="small text-muted">
              Số lượng trong kho: {{ $loai->kho_tai_san_sum_so_luong ?? 0 }}
            </p>
            <a href="{{ route('kho.related', $loai->id) }}" class="btn-dergin btn-dergin--info">
              <i class="fa fa-boxes-stacked"></i><span>Xem tài sản</span>
            </a>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- 🔢 Phân trang --}}
  <div class="d-flex justify-content-center mt-4">
    {{ $loaiTaiSan->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
