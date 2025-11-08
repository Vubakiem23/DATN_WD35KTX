@extends('admin.layouts.admin')

@section('title', 'Danh sách loại tài sản')

@section('content')
<div class="container mt-4">

  @push('styles')
  <style>
    .page-title{font-size:1.5rem;font-weight:700;color:#1f2937}
    .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
    .btn-dergin i{font-size:.8rem}
    .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
    .btn-dergin--success{background:linear-gradient(135deg,#10b981 0%,#22c55e 100%)}

    .asset-table-wrapper{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:1.25rem}
    .asset-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
    .asset-table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;border:none;padding-bottom:.75rem}
    .asset-table tbody tr{background:#f9fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
    .asset-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(15,23,42,0.08)}
    .asset-table tbody td{border:none;vertical-align:middle;padding:1rem .95rem}
    .asset-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
    .asset-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}

    .filter-card {
      background: #f8f9fa;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
      padding: 16px 20px;
      margin-bottom: 20px;
      border-left: 5px;
    }
    .filter-card label {
      font-weight: 600;
      color: #495057;
    }
    .filter-card input {
      border-radius: 10px;
    }
    .filter-card .btn {
      border-radius: 10px;
    }
    .loai-actions{
      display:flex;
      justify-content:flex-end;
      align-items:center;
      gap:6px;
      white-space:nowrap;
    }
    .loai-actions .btn-action{width:auto;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px;margin:0}
    .loai-actions .btn-action i{font-size:14px}
    .loai-actions .btn-dergin{min-width:92px}
    .loai-actions .btn-dergin span{line-height:1;white-space:nowrap}
  </style>
  @endpush


    <h4 class="page-title mb-0">📋 Danh sách loại tài sản</h4>
    
  

  {{-- 🔔 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- ✅ Form lọc tìm kiếm --}}
  <form action="{{ route('loaitaisan.index') }}" method="GET" class="filter-card">
    <div class="row g-3 align-items-end">
      <div class="col-md-5">
        <label class="form-label">Từ khóa tìm kiếm</label>
        <div class="input-group">
          <span class="input-group-text bg-white"><i class="fa fa-search text-muted"></i></span>
          <input type="text" name="keyword" value="{{ request('keyword') }}" 
                 class="form-control" placeholder="Nhập tên loại tài sản...">
        </div>
      </div>
      <div class="col-md-7 d-flex gap-2 justify-content-end">
        <button type="submit" class="btn-dergin btn-dergin--info">
          <i class="fa fa-filter"></i><span>Lọc</span>
        </button>
        <a href="{{ route('loaitaisan.index') }}" class="btn-dergin btn-dergin--muted"><i class="fa fa-rotate-left"></i><span>Làm mới</span></a>
        <a href="{{ route('loaitaisan.create') }}" class="btn-dergin btn-dergin--success"><i class="fa fa-plus"></i><span>Thêm mới</span></a>
      </div>
    </div>
  </form>

  {{-- 🧾 Bảng danh sách --}}
  <div class="asset-table-wrapper">
    <div class="table-responsive">
      <table class="table align-middle asset-table">
        <thead>
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
                 class="btn-dergin btn-action" 
                 title="Sửa">
                <i class="fa fa-pencil"></i><span>Sửa</span>
              </a>

              <form action="{{ route('loaitaisan.destroy', $loai->id) }}" 
                    method="POST" class="d-inline"
                    onsubmit="return confirm('Bạn có chắc muốn xóa loại tài sản này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-dergin btn-dergin--muted btn-action" title="Xóa">
                  <i class="fa fa-trash"></i><span>Xóa</span>
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
