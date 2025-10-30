@extends('admin.layouts.admin')

@section('title', 'Danh sách loại tài sản')

@section('content')
<div class="container mt-4">

  @push('styles')
  <style>
    .loai-actions .btn-action {
      width: 40px;
      height: 36px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 10px;
      margin: 0 2px;
    }
    .loai-actions .btn-action i {
      font-size: 14px;
    }
  </style>
  @endpush

  <h4 class="mb-3">📋 Danh sách loại tài sản</h4>

  {{-- 🔔 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- ✅ Form lọc tìm kiếm --}}
  <form action="{{ route('loaitaisan.index') }}" method="GET" class="mb-4">
    <div class="input-group">
      <span class="text-white">
        <i class="bi bi-search"></i>
      </span>
      <input type="text" name="keyword" value="{{ request('keyword') }}" 
             class="form-control" placeholder="Nhập tên loại tài sản cần tìm...">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-filter"></i> Lọc
      </button>
      <a href="{{ route('loaitaisan.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-clockwise"></i> Làm mới
      </a>
    </div>
  </form>

  {{-- 🔘 Nút thêm mới --}}
  <a href="{{ route('loaitaisan.create') }}" class="btn btn-success mb-3">➕ Thêm loại tài sản</a>

  {{-- 🧾 Bảng danh sách --}}
  <div class="card">
    <div class="card-body p-0">
      <table class="table table-bordered table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Hình ảnh</th>
            <th>Mã loại</th>
            <th>Tên loại</th>
            <th>Mô tả</th>
            <th>Ngày tạo</th>
            <th class="text-end" style="width: 140px;">Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($loais as $loai)
          <tr>
            <td>{{ $loais->firstItem() + $loop->index }}</td>
            <td class="text-center">
              @if ($loai->hinh_anh && file_exists(public_path('uploads/loai/'.$loai->hinh_anh)))
                <img src="{{ asset('uploads/loai/'.$loai->hinh_anh) }}"
                     alt="{{ $loai->ten_loai }}"
                     style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
              @else
                <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                     style="width:70px;height:70px;">
                  <small>Không có ảnh</small>
                </div>
              @endif
            </td>
            <td>{{ $loai->ma_loai }}</td>
            <td>{{ $loai->ten_loai }}</td>
            <td>{{ $loai->mo_ta }}</td>
            <td>{{ $loai->created_at->format('d/m/Y') }}</td>

            {{-- 🎯 Nút hành động --}}
            <td class="text-end loai-actions">
              <a href="{{ route('loaitaisan.edit', $loai->id) }}" 
                 class="btn btn-outline-primary btn-action" 
                 title="Sửa">
                <i class="fa fa-pencil"></i>
              </a>

              <form action="{{ route('loaitaisan.destroy', $loai->id) }}" 
                    method="POST" class="d-inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa loại tài sản này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger btn-action" title="Xóa">
                  <i class="fa fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Không tìm thấy loại tài sản nào.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- ✅ Phân trang --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $loais->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
