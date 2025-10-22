@extends('admin.layouts.admin')

@section('title', 'Quản lý tài sản phòng')

@section('content')
<style>
  .pagination-info,
  .small.text-muted {
    display: none !important;
  }
</style>

<div class="container-fluid">

  <form method="GET" action="{{ route('taisan.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
      <input name="search" value="{{ request('search') }}" class="form-control"
        placeholder="Tìm theo mã hoặc tên tài sản...">
    </div>

    <div class="col-md-3">
      <select name="phong_id" class="form-select" onchange="this.form.submit()">
        <option value="">-- Tất cả phòng --</option>
        @foreach($phongs as $phong)
        <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
          {{ $phong->ten_phong }}
        </option>
        @endforeach
      </select>
    </div>

    <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
      <div class="text-center">
        <button type="submit" class="btn btn-secondary me-2">Tìm kiếm</button>
        <a href="{{ route('taisan.create') }}" class="btn btn-primary">+ Thêm</a>
      </div>
    </div>
  </form>


  {{-- 🔔 Thông báo --}}
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- 📋 Bảng danh sách --}}
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Mã tài sản</th>
          <th>Tên tài sản</th>
          <th>Ảnh</th>
          <th>Phòng</th>
          <th>Số lượng</th>
          <th>Tình trạng</th>
          <th>Tình trạng hiện tại</th> {{-- 🆕 --}}
          <th>Ghi chú</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($listTaiSan as $index => $item)
        <tr>
          <td>{{ $index + 1 }}</td>
          <td>{{ $item->khoTaiSan->ma_tai_san ?? '—' }}</td>
          <td>{{ $item->khoTaiSan->ten_tai_san ?? '—' }}</td>
          <td>
            @if(!empty($item->khoTaiSan->hinh_anh))
            <img src="{{ asset('uploads/kho/' . $item->khoTaiSan->hinh_anh) }}" alt="Ảnh" width="70" class="rounded">
            @else
            <span class="badge bg-secondary">Không có</span>
            @endif
          </td>
          <td>{{ $item->phong->ten_phong ?? 'Chưa gán' }}</td>
          <td>{{ $item->so_luong }}</td>

          {{-- Cột Tình trạng gốc --}}
          <td>
            <span class="badge 
        @if($item->tinh_trang == 'mới') bg-success
        @elseif($item->tinh_trang == 'cũ') bg-secondary
        @elseif($item->tinh_trang == 'bảo trì') bg-warning text-dark
        @elseif($item->tinh_trang == 'hỏng') bg-danger
        @else bg-info @endif">
              {{ ucfirst($item->tinh_trang) }}
            </span>
          </td>

          {{-- 🆕 Cột Tình trạng hiện tại --}}
          <td>
            <span class="badge 
        @if($item->tinh_trang_hien_tai == 'mới') bg-success
        @elseif($item->tinh_trang_hien_tai == 'cũ') bg-secondary
        @elseif($item->tinh_trang_hien_tai == 'bảo trì') bg-warning text-dark
        @elseif($item->tinh_trang_hien_tai == 'hỏng') bg-danger
        @else bg-info @endif">
              {{ ucfirst($item->tinh_trang_hien_tai ?? 'Không rõ') }}
            </span>
          </td>

          <td>{{ $item->ghi_chu ?? '-' }}</td>

          {{-- Hành động --}}
          <td>
            <a href="{{ route('taisan.edit', $item->id) }}" class="btn btn-sm btn-warning">Sửa</a>

            {{-- 🆕 Nút lên lịch bảo trì --}}
            <a href="{{ route('lichbaotri.create', ['taisan_id' => $item->id]) }}" class="btn btn-sm btn-primary">
              Lên lịch bảo trì
            </a>


            <form action="{{ route('taisan.destroy', $item->id) }}" method="POST" style="display:inline;">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Xóa tài sản này khỏi phòng?')" class="btn btn-sm btn-danger">Xóa</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" class="text-center text-muted">Không có tài sản nào trong phòng</td>
        </tr>
        @endforelse
      </tbody>

    </table>

    <div class="d-flex justify-content-center mt-3">
      {{ $listTaiSan->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
  </div>

</div>
@endsection