@extends('admin.layouts.admin')

@section('title', 'Quản lý tài sản phòng')

@section('content')
<div class="container mt-4">

  
    <h3 class="asset-page__title mb-0">🏢 Quản lý tài sản phòng</h3>
  <div class="mb-4">
    <a href="{{ route('taisan.create') }}" class="btn btn-dergin btn-dergin--info">
      <i class="fa fa-plus"></i><span>Thêm tài sản vào phòng "Tùy Chọn"</span>
    </a>
    </div>

  {{-- 🎨 Bộ lọc đẹp như trang lịch bảo trì --}}
  <div class="filter-card mb-4">
    <form method="GET" action="{{ route('taisan.index') }}" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label"><i class="fa fa-magnifying-glass text-primary"></i> Tìm kiếm</label>
        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
               placeholder="Nhập mã hoặc tên tài sản...">
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-door-open text-primary"></i> Phòng</label>
        <select name="phong_id" class="form-select form-control">
          <option value="">-- Tất cả phòng --</option>
          @foreach($phongs as $phong)
            <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
              {{ $phong->ten_phong }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-circle-info text-primary"></i> Tình trạng</label>
        <select name="tinh_trang" class="form-select form-control">
          <option value="">-- Tất cả tình trạng --</option>
          <option value="mới" {{ request('tinh_trang') == 'mới' ? 'selected' : '' }}>Mới</option>
          <option value="cũ" {{ request('tinh_trang') == 'cũ' ? 'selected' : '' }}>Cũ</option>
          <option value="bảo trì" {{ request('tinh_trang') == 'bảo trì' ? 'selected' : '' }}>Bảo trì</option>
          <option value="hỏng" {{ request('tinh_trang') == 'hỏng' ? 'selected' : '' }}>Hỏng</option>
        </select>
      </div>

      <div class="col-md-2 d-flex gap-2 filter-btns">
        <button type="submit" class="btn btn-success flex-fill">
          <i class="fa fa-filter"></i> Lọc
        </button>
        @if(request('search') || request('phong_id') || request('tinh_trang'))
          <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary flex-fill">
            <i class="fa fa-rotate-left"></i> Đặt lại
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- 🔔 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <h4 class="mb-2">📋 Danh sách tài sản</h4>

  {{-- 🧱 Bảng hiển thị --}}
  <div class="asset-table-wrapper">
    <div class="table-responsive">
      <table class="table align-middle asset-table">
        <thead>
          <tr>
            <th class="text-center">#</th>
            <th class="text-center">Ảnh</th>
            <th>Mã tài sản</th>
            <th>Tên tài sản</th>
            <th>Phòng</th>
            <th>Sinh viên sử dụng</th>
            <th>Tình trạng</th>
            <th>Hiện tại</th>
            <th>Ghi chú</th>
            <th class="text-end">Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse($listTaiSan as $item)
          <tr class="asset-row">
            <td class="text-center">{{ $loop->iteration + ($listTaiSan->currentPage() - 1) * $listTaiSan->perPage() }}</td>

            {{-- Ảnh --}}
            <td class="text-center asset-thumb-cell">
              @if(!empty($item->khoTaiSan->hinh_anh))
                <div class="asset-thumb mx-auto">
                  <img src="{{ asset('storage/' . $item->khoTaiSan->hinh_anh) }}" alt="Ảnh tài sản">
                </div>
              @else
                <div class="asset-thumb mx-auto bg-light text-muted d-flex align-items-center justify-content-center">
                  <small class="small">Không ảnh</small>
                </div>
              @endif
            </td>

            <td>{{ $item->khoTaiSan->ma_tai_san ?? '—' }}</td>
            <td>{{ $item->khoTaiSan->ten_tai_san ?? '—' }}</td>
            <td>{{ $item->phong->ten_phong ?? 'Chưa gán' }}</td>
            <td>
              @php
                $sinhViens = $item->slots->pluck('sinhVien.ho_ten')->filter()->unique();
              @endphp
              @if($sinhViens->isNotEmpty())
                {{ $sinhViens->implode(', ') }}
              @else
                <span class="text-muted">Chưa có</span>
              @endif
            </td>

            <td>
              <span class="badge 
                @if($item->tinh_trang == 'mới') bg-success
                @elseif($item->tinh_trang == 'cũ') bg-secondary
                @elseif($item->tinh_trang == 'bảo trì') bg-warning text-dark
                @elseif($item->tinh_trang == 'hỏng') bg-danger
                @else bg-light @endif">
                {{ ucfirst($item->tinh_trang) }}
              </span>
            </td>

            <td>
              <span class="badge 
                @if($item->tinh_trang_hien_tai == 'Bình thường') bg-success text-white
                @elseif($item->tinh_trang_hien_tai == 'Cũ') bg-secondary
                @elseif($item->tinh_trang_hien_tai == 'Đang bảo trì') bg-warning text-dark
                @elseif($item->tinh_trang_hien_tai == 'Hỏng') bg-danger
                @else bg-light @endif">
                {{ ucfirst($item->tinh_trang_hien_tai ?? 'Chưa cập nhật') }}
              </span>
            </td>

            <td>{{ $item->ghi_chu ?? '-' }}</td>

            {{-- Hành động --}}
            <td class="action-cell">
              <a href="{{ route('taisan.edit', $item->id) }}" class="btn btn-dergin" title="Sửa">
                <i class="fa fa-pencil"></i><span>Sửa</span>
              </a>

              <a href="{{ route('lichbaotri.create', ['taisan_id' => $item->id]) }}"
                 class="btn btn-dergin btn-dergin--muted" title="Lên lịch bảo trì">
                <i class="fa fa-calendar"></i><span>Bảo trì</span>
              </a>

              <button type="button" class="btn btn-dergin btn-dergin--info btn-xemchitiet"
                      data-id="{{ $item->id }}" title="Xem chi tiết">
                <i class="fa fa-eye"></i><span>Chi tiết</span>
              </button>

              <form action="{{ route('taisan.destroy', $item->id) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Xóa tài sản này khỏi phòng?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-dergin btn-dergin--danger" title="Xóa">
                  <i class="fa fa-trash"></i><span>Xóa</span>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center text-muted py-3">Không có tài sản nào trong phòng.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- 📄 Phân trang --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $listTaiSan->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>

{{-- 📦 Modal xem chi tiết --}}
<div class="modal fade" id="modalTaiSan" tabindex="-1" aria-labelledby="modalTaiSanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalTaiSanLabel">🔍 Chi tiết tài sản phòng</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div class="spinner-border text-info" role="status">
          <span class="visually-hidden">Đang tải...</span>
        </div>
        <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

{{-- 🧩 CSS & JS --}}
@push('styles')
<style>
  .asset-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
  .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
  .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
  .btn-dergin i{font-size:.8rem}
  .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
  .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
  .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}

  .asset-table-wrapper{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:1.25rem}
  .asset-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
  .asset-table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;border:none;padding-bottom:.75rem}
  .asset-table tbody tr{background:#f9fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
  .asset-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(15,23,42,0.08)}
  .asset-table tbody td{border:none;vertical-align:middle;padding:1rem .95rem}
  .asset-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
  .asset-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
  .asset-thumb-cell{width:96px}
  .asset-thumb{width:64px;height:64px;border-radius:14px;overflow:hidden;flex:0 0 64px;background:#e9ecef;display:flex;align-items:center;justify-content:center}
  .asset-thumb img{width:100%;height:100%;object-fit:cover}

  .action-cell{
    display:flex;
    align-items:center;
    justify-content:flex-end;
    gap:.5rem;
    white-space:nowrap;
  }
  .action-cell form{margin:0}
  .action-cell .btn{line-height:1}
  .action-cell .btn-dergin{min-width:92px}
  .action-cell .btn-dergin span{line-height:1;white-space:nowrap}

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
  .filter-btns i {
    margin-right: 5px;
  }
</style>
@endpush

<script>
  $(document).ready(function() {
    $('.btn-xemchitiet').click(function() {
      let id = $(this).data('id');
      let modal = $('#modalTaiSan');

      modal.modal('show');
      modal.find('.modal-body').html(`
        <div class="text-center py-4">
          <div class="spinner-border text-info" role="status"></div>
          <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
        </div>
      `);

      $.ajax({
        url: '{{ route("taisan.showModal", "") }}/' + id,
        type: 'GET',
        success: function(response) {
          modal.find('.modal-body').html(response.data);
        },
        error: function() {
          modal.find('.modal-body').html('<p class="text-danger text-center">Không thể tải dữ liệu tài sản.</p>');
        }
      });
    });
  });
</script>
@endsection
