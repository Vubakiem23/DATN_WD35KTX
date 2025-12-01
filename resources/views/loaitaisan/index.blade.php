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

    /* form tìm kiếm dùng style chung search-bar trong admin */
    .action-cell{
      position:relative;
      text-align:right;
      white-space:nowrap;
    }
    .action-menu{
      display:inline-flex;
      justify-content:flex-end;
    }
    .action-menu.dropdown{position:relative;}
    .action-menu .action-gear{
      min-width:40px;
      padding:.45rem .7rem;
      border-radius:999px;
    }
    .action-menu .dropdown-menu{
      display:none;
      position:absolute;
      top:50% !important;
      right:110%;
      left:auto;
      transform:translateY(-50%);
      z-index:1050;
      min-width:190px;
      border-radius:16px;
      padding:.4rem 0;
      margin:0;
      border:1px solid #e5e7eb;
      box-shadow:0 16px 40px rgba(15,23,42,.18);
      font-size:.82rem;
      background:#fff;
    }
    .action-menu .dropdown-menu.show{display:block;}
    .action-menu .dropdown-item{
      display:flex;
      align-items:center;
      gap:.55rem;
      padding:.42rem .9rem;
      color:#4b5563;
      font-weight:600;
    }
    .action-menu .dropdown-item i{
      width:16px;
      text-align:center;
      font-size:.82rem;
    }
    .action-menu .dropdown-item:hover{
      background:#eef2ff;
      color:#111827;
    }
    .action-menu .dropdown-item.text-danger{color:#dc2626;}
    .action-menu .dropdown-item.text-danger:hover{background:#fee2e2;color:#b91c1c;}
  </style>
  @endpush


    <h4 class="page-title mb-0"> Danh sách loại tài sản</h4>
    <p class="text-muted mb-0">Theo dõi và tổ chức loại tài sản.</p>
    
  

  {{-- 🔔 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  {{-- ✅ Thanh tìm kiếm giống trang sinh viên --}}
  <form action="{{ route('loaitaisan.index') }}" method="GET" class="mb-3 search-bar">
    <div class="input-group">
      <input type="text" name="keyword" value="{{ request('keyword') }}"
             class="form-control" placeholder="Nhập tên loại tài sản...">

      <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>

      <button type="submit" name="filter" value="1" class="btn btn-outline-primary">
        <i class="fa fa-filter mr-1"></i> Lọc
      </button>

      @if (request()->filled('keyword'))
        <a href="{{ route('loaitaisan.index') }}" class="btn btn-outline-secondary">Xóa</a>
      @endif

      <a href="{{ route('loaitaisan.create') }}" class="btn btn-primary ms-auto">
        <i class="fa fa-plus"></i><span> Thêm mới</span>
      </a>
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
            <td class="text-end action-cell">
              <div class="action-menu dropdown position-relative">
                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear" title="Tác vụ">
                  <i class="fa fa-gear"></i>
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a href="{{ route('loaitaisan.edit', $loai->id) }}" class="dropdown-item">
                      <i class="fa fa-pencil text-primary"></i>
                      <span>Sửa</span>
                    </a>
                  </li>
                  <li>
                    <button type="button"
                      class="dropdown-item text-danger btn-delete-loai"
                      data-form-id="delete-loai-{{ $loai->id }}">
                      <i class="fa fa-trash"></i>
                      <span>Xóa</span>
                    </button>
                  </li>
                </ul>
              </div>
              <form id="delete-loai-{{ $loai->id }}" action="{{ route('loaitaisan.destroy', $loai->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
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
@push('scripts')
<script>
  $(function() {
    $(document).on('click', function(e) {
      const $target = $(e.target);
      const $gear = $target.closest('.action-gear');

      if ($gear.length) {
        e.preventDefault();
        const $wrapper = $gear.closest('.action-menu');
        const $menu = $wrapper.find('.dropdown-menu').first();
        const isOpen = $menu.hasClass('show');
        $('.action-menu .dropdown-menu').removeClass('show');
        if (!isOpen) {
          $menu.addClass('show');
        }
        return;
      }

      if (!$target.closest('.action-menu .dropdown-menu').length) {
        $('.action-menu .dropdown-menu').removeClass('show');
      }
    });

    $(document).on('click', '.action-menu .dropdown-item', function() {
      $('.action-menu .dropdown-menu').removeClass('show');
    });

    $(document).on('click', '.btn-delete-loai', function(e) {
      e.preventDefault();
      const formId = $(this).data('form-id');
      if (!formId) {
        return;
      }
      if (confirm('Bạn có chắc muốn xóa loại tài sản này không?')) {
        const form = document.getElementById(formId);
        if (form) {
          form.submit();
        }
      }
    });
  });
</script>
@endpush
@endsection
