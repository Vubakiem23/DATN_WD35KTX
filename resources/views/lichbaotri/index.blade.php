@extends('admin.layouts.admin')

@section('title', 'Quản lý lịch bảo trì')

@section('content')
<div class="container-fluid">

  {{-- Thanh công cụ lọc --}}
  <form method="GET" action="{{ route('lichbaotri.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
      <input name="ten_tai_san" value="{{ request('ten_tai_san') }}" class="form-control" placeholder="Tìm theo tên tài sản">
    </div>

    <div class="col-md-3">
      <select name="trang_thai" class="form-select">
        <option value="">-- Trạng thái --</option>
        <option value="Chờ" {{ request('trang_thai') == 'Chờ' ? 'selected' : '' }}>Chờ</option>
        <option value="Đang bảo trì" {{ request('trang_thai') == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
        <option value="Hoàn thành" {{ request('trang_thai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
      </select>
    </div>

    <div class="col-md-3">
      <input type="date" name="ngay_bao_tri" value="{{ request('ngay_bao_tri') }}" class="form-control" placeholder="Ngày bảo trì">
    </div>

    <div class="col-md-3 text-end">
      <button type="submit" class="btn btn-secondary me-2">Lọc</button>
      <a href="{{ route('lichbaotri.create') }}" class="btn btn-primary">+ Lên lịch mới</a>
    </div>
  </form>

  {{-- Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- Bảng dữ liệu --}}
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>#</th>
          <th>Tài sản</th>
          <th>Ngày bảo trì</th>
          <th>Mô tả</th>
          <th>Trạng thái</th>
          <th>Ngày hoàn thành</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        @forelse($lich as $index => $l)
        <tr @if($l->trang_thai == 'Hoàn thành') style="background-color:#e8ffe8;" @endif>
          <td>{{ $index + 1 }}</td>
          <td>{{ $l->taiSan->ten_tai_san ?? 'Không xác định' }}</td>
          <td>{{ $l->ngay_bao_tri }}</td>
          <td>{{ $l->mo_ta ?? '-' }}</td>
          <td>
            @if($l->trang_thai == 'Hoàn thành')
              <span class="badge bg-success">Hoàn thành</span>
            @elseif($l->trang_thai == 'Đang bảo trì')
              <span class="badge bg-warning text-dark">Đang bảo trì</span>
            @else
              <span class="badge bg-secondary">Chờ</span>
            @endif
          </td>
          <td>{{ $l->ngay_hoan_thanh ?? '-' }}</td>
          <td>
            {{-- Nút sửa --}}
            <a href="{{ route('lichbaotri.edit', $l->id) }}" class="btn btn-sm btn-primary">Sửa</a>

            {{-- Nút xóa --}}
            <form action="{{ route('lichbaotri.destroy', $l->id) }}" method="POST" style="display:inline;">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa lịch này?')">Xóa</button>
            </form>

            {{-- Nút hoàn thành --}}
            @if($l->trang_thai != 'Hoàn thành')
            <form action="{{ route('lichbaotri.hoanthanh', $l->id) }}" method="POST" style="display:inline;">
              @csrf
              @method('PATCH')
              <button class="btn btn-sm btn-success" onclick="return confirm('Đánh dấu lịch này đã hoàn thành?')">
                ✅ Hoàn thành
              </button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="text-center text-muted">Không có lịch bảo trì nào</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
