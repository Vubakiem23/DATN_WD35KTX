@extends('admin.layouts.admin')

@section('title', 'Danh sách loại tài sản')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">📋 Danh sách loại tài sản</h4>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <a href="{{ route('loaitaisan.create') }}" class="btn btn-success mb-3">➕ Thêm loại tài sản</a>

  <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Hình ảnh</th>
        <th>Mã loại</th>
        <th>Tên loại</th>
        <th>Mô tả</th>
        <th>Ngày tạo</th>
        <th class="text-center" style="width: 150px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($loais as $loai)
        <tr>
          <td>{{ $loop->iteration }}</td>

          {{-- Hình ảnh --}}
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

          <td class="text-center">
            <a href="{{ route('loaitaisan.edit', $loai->id) }}" class="btn btn-warning btn-sm">✏️ Sửa</a>
            <form action="{{ route('loaitaisan.destroy', $loai->id) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Bạn có chắc muốn xóa loại tài sản này không?');">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">🗑️ Xóa</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endsection
