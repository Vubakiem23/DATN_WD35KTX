@extends('admin.layouts.admin')

@section('title', 'Quản lý tài sản')

@section('content')
<div class="container-fluid">

  {{-- Thanh công cụ --}}
  <form method="GET" action="{{ route('taisan.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
      <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm tên tài sản">
    </div>
    <div class="col-md-2">
      <input name="tinhtrang" value="{{ request('tinhtrang') }}" class="form-control" placeholder="Tình trạng">
    </div>

    <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
      <div>
        <button type="submit" class="btn btn-secondary me-2">Lọc</button>
        <a href="{{ route('taisan.create') }}" class="btn btn-primary text-center">+ Thêm </a>
      </div>
    </div>
  </form>

  {{-- 🔔 Thông báo trạng thái (di chuyển lên đây) --}}
  @if(session('status'))
  <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger">{!! session('error') !!}</div>
  @endif

  {{-- Dòng thống kê --}}
  <p class="mb-2 mt-2">
    <strong>Loại thiết bị, tài sản:</strong> {{ $totals['total'] }} |
    <span class="badge bg-success">Mới: {{ $totals['moi'] }}</span>
    <span class="badge bg-secondary">Cũ: {{ $totals['cu'] }}</span>
    <span class="badge bg-warning text-dark">Bảo trì: {{ $totals['baotri'] }}</span>
    <span class="badge bg-danger">Đã hỏng: {{ $totals['hong'] }}</span>
  </p>

  {{-- Bảng dữ liệu --}}
  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Tên tài sản</th>
          <th>Số lượng</th>
          <th>Tình trạng ban đầu</th>
          <th>Tình trạng hiện tại</th>
          <th>Phòng</th>
          <th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($listTaiSan as $item)
        <tr>
          <td>{{ $item->id }}</td>
          <td>{{ $item->ten_tai_san }}</td>
          <td>{{ $item->so_luong }}</td>
          <td><span class="badge bg-success">{{ $item->tinh_trang }}</span></td>
          <td><span class="badge bg-info">{{ $item->tinh_trang_hien_tai ?? 'Chưa cập nhật' }}</span></td>
          <td><span class="badge bg-warning">{{ $item->ten_phong ?? 'Chưa gán phòng' }}</span></td>
          <td>
            <a href="{{ route('taisan.edit', $item->id) }}" class="btn btn-sm btn-primary">Sửa</a>

            {{-- 🔧 Nút lên lịch bảo trì --}}
            <a href="{{ route('lichbaotri.create', ['tai_san_id' => $item->id]) }}"
              class="btn btn-sm btn-info">
              Lên lịch bảo trì
            </a>

            @if($item->tinh_trang_hien_tai != 'đã hỏng')
            <form action="{{ route('taisan.baohong', $item->id) }}" method="POST" style="display:inline">
              @csrf
              @method('PUT')
              <button class="btn btn-sm btn-warning" onclick="return confirm('Xác nhận báo hỏng tài sản này?')">
                Báo hỏng
              </button>
            </form>
            @endif

            <form action="{{ route('taisan.destroy', $item->id) }}" method="POST" style="display:inline">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa tài sản này?')">Xóa</button>
            </form>
          </td>

        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted">Không có tài sản nào</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection