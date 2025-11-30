@extends('admin.layouts.admin')

@section('title', 'Quản lý tài sản phòng')

@section('content')
<div class="container mt-4">


  <h3 class="asset-page__title mb-0">Quản lý tài sản phòng</h3>
  <p class="text-muted mb-0">Theo dõi và tổ chức tài sản trong các phòng.</p>
  <div class="mb-4">
    <a href="{{ route('taisan.create') }}" class="btn btn-dergin btn-dergin--info">
      <i class="fa fa-plus"></i><span>Thêm tài sản vào phòng "Tùy Chọn"</span>
    </a>
  </div>

  <!-- Ô tìm kiếm -->
  <form method="GET" class="mb-3 search-bar">
    <div class="input-group">
      <input type="text" name="search" value="{{ request('search') }}" class="form-control"
        placeholder="Tìm kiếm mã hoặc tên tài sản...">
      <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
      <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
        <i class="fa fa-filter mr-1"></i> Bộ lọc
      </button>

      @if (!empty(request('search')) || request()->filled('phong_id') || request()->filled('tinh_trang'))
        <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary">Xóa</a>
      @endif
    </div>
  </form>

  {{-- 🔔 Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <h4 class="mb-2"> Danh sách tài sản</h4>

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
            <td class="action-cell text-end">
              <div class="action-menu  position-relative">
                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                  <i class="fa fa-gear"></i>
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <a href="{{ route('taisan.edit', $item->id) }}" class="dropdown-item">
                      <i class="fa fa-pencil text-primary"></i>
                      <span>Sửa</span>
                    </a>
                  </li>
                  @if($item->tinh_trang_hien_tai !== 'Đang bảo trì')
                  <li>
                    <a href="{{ route('lichbaotri.create', ['taisan_id' => $item->id]) }}" class="dropdown-item">
                      <i class="fa fa-calendar text-primary"></i>
                      <span>Bảo trì</span>
                    </a>
                  </li>
                  @endif
                  <li>
                    <button type="button"
                      class="dropdown-item btn-xemchitiet"
                      data-id="{{ $item->id }}"
                      data-url="{{ route('taisan.showModal', $item->id) }}"
                      data-bs-toggle="modal" data-bs-target="#modalTaiSan"
                      data-toggle="modal" data-target="#modalTaiSan">
                      <i class="fa fa-eye text-info"></i>
                      <span>Chi tiết</span>
                    </button>
                  </li>
                  <li>
                    <button type="button"
                      class="dropdown-item text-danger btn-delete-taisan"
                      data-form-id="delete-taisan-{{ $item->id }}">
                      <i class="fa fa-trash"></i>
                      <span>Xóa</span>
              </button>
                  </li>
                </ul>
              </div>
              <form id="delete-taisan-{{ $item->id }}" action="{{ route('taisan.destroy', $item->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
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
  .asset-page__title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
  }

  .btn-dergin {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .4rem .9rem;
    border-radius: 999px;
    font-weight: 600;
    font-size: .72rem;
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
    box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
    transition: transform .2s ease, box-shadow .2s ease
  }

  .btn-dergin:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
    color: #fff
  }

  .btn-dergin i {
    font-size: .8rem
  }

  .btn-dergin--muted {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%)
  }

  .btn-dergin--info {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%)
  }

  .btn-dergin--danger {
    background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%)
  }

  .asset-table-wrapper {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    padding: 1.25rem
  }

  .asset-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0 12px
  }

  .asset-table thead th {
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #6c757d;
    border: none;
    padding-bottom: .75rem
  }

  .asset-table tbody tr {
    background: #f9fafc;
    border-radius: 16px;
    transition: transform .2s ease, box-shadow .2s ease
  }

  .asset-table tbody tr:hover {
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08)
  }

  .asset-table tbody td {
    border: none;
    vertical-align: middle;
    padding: 1rem .95rem
  }

  .asset-table tbody tr td:first-child {
    border-top-left-radius: 16px;
    border-bottom-left-radius: 16px
  }

  .asset-table tbody tr td:last-child {
    border-top-right-radius: 16px;
    border-bottom-right-radius: 16px
  }

  .asset-thumb-cell {
    width: 96px
  }

  .asset-thumb {
    width: 64px;
    height: 64px;
    border-radius: 14px;
    overflow: hidden;
    flex: 0 0 64px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center
  }

  .asset-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover
  }

  .action-cell {
    position: relative;
   
  }

  .action-menu {
    display: inline-flex;
    justify-content: flex-end;
  }

  .action-menu.dropdown {
    position: relative;
  }

  .action-menu .action-gear {
    min-width: 40px;
    padding: .45rem .7rem;
    border-radius: 999px;
  }

 .action-menu .dropdown-menu {
    display: none;
    position: absolute;
    top: 0 !important;         /* ✔ Không còn nằm giữa */
    right: 45px !important;    /* ✔ Sát nút gear */
    left: auto;
    transform: none !important;
    z-index: 9999 !important;   
    min-width: 180px;
    border-radius: 16px;
    padding: .4rem 0;
    margin: 0;
    border: 1px solid #e5e7eb;
    box-shadow: 0 16px 40px rgba(15,23,42,.18);
    font-size: .82rem;
    background: #fff;
}

  .action-menu .dropdown-menu.show {
    display: block;
  }

  .action-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: .55rem;
    padding: .42rem .9rem;
    color: #4b5563;
    font-weight: 600;
  }

  .action-menu .dropdown-item i {
    width: 16px;
    text-align: center;
    font-size: .82rem;
  }

  .action-menu .dropdown-item:hover {
    background: #eef2ff;
    color: #111827;
  }

  .action-menu .dropdown-item.text-danger {
    color: #dc2626;
  }

  .action-menu .dropdown-item.text-danger:hover {
    background: #fee2e2;
    color: #b91c1c;
  }

  .action-menu .dropdown-item.text-success {
    color: #15803d;
  }

  .action-menu .dropdown-item.text-success:hover {
    background: #dcfce7;
    color: #166534;
  }

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

    $(document).on('click', '.btn-delete-taisan', function(e) {
      e.preventDefault();
      const formId = $(this).data('form-id');
      if (!formId) {
        return;
      }
      if (confirm('Xóa tài sản này khỏi phòng?')) {
        const form = document.getElementById(formId);
        if (form) {
          form.submit();
        }
      }
    });

    // Dùng ủy quyền sự kiện để đảm bảo luôn bắt được click
    $(document).on('click', '.btn-xemchitiet', function() {
      let id = $(this).data('id');
      let url = $(this).data('url');
      let modal = $('#modalTaiSan');

      // Tương thích Bootstrap 4/5 khi hiển thị modal
      try {
        if (window.bootstrap && window.bootstrap.Modal) {
          window.bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTaiSan')).show();
        } else {
          modal.modal('show');
        }
      } catch (e) {
      modal.modal('show');
      }
      modal.find('.modal-body').html(`
        <div class="text-center py-4">
          <div class="spinner-border text-info" role="status"></div>
          <p class="mt-2 text-muted">Đang tải dữ liệu...</p>
        </div>
      `);

      if (!url) {
        modal.find('.modal-body').html('<p class="text-danger text-center">Không xác định được URL chi tiết tài sản.</p>');
        return;
      }

      $.ajax({
        url: url,
        type: 'GET',
        // Nhận HTML thẳng từ server để gắn vào modal
        dataType: 'html',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        timeout: 15000,
        success: function(response) {
          // Server trả HTML → gắn trực tiếp
          modal.find('.modal-body').html(response || '<p class="text-muted text-center">Không có dữ liệu hiển thị.</p>');
        },
        error: function(xhr) {
          console.error('Tải chi tiết tài sản thất bại:', {
            status: xhr.status,
            statusText: xhr.statusText,
            responseText: xhr.responseText
          });
          modal.find('.modal-body').html(
            `<div class="text-center text-danger">
               <p class="mb-1">Không thể tải dữ liệu tài sản.</p>
               <small>Mã lỗi: ${xhr.status} ${xhr.statusText}</small>
             </div>`
          );
        },
        complete: function() {
          // Không để spinner kẹt nếu có sự cố hiếm gặp
        }
      });
    });
  });
</script>
@endpush

{{-- MODAL BỘ LỌC --}}
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Bộ lọc tài sản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="GET" action="{{ route('taisan.index') }}" id="filterForm">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="small text-muted">Tìm kiếm</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="Mã hoặc tên tài sản">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="small text-muted">Phòng</label>
                                <select name="phong_id" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($phongs as $phong)
                                        <option value="{{ $phong->id }}" @selected(request('phong_id') == $phong->id)>
                                            {{ $phong->ten_phong }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="small text-muted">Tình trạng</label>
                                <select name="tinh_trang" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    <option value="mới" @selected(request('tinh_trang') == 'mới')>Mới</option>
                                    <option value="cũ" @selected(request('tinh_trang') == 'cũ')>Cũ</option>
                                    <option value="bảo trì" @selected(request('tinh_trang') == 'bảo trì')>Bảo trì</option>
                                    <option value="hỏng" @selected(request('tinh_trang') == 'hỏng')>Hỏng</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Mở modal bộ lọc tài sản (chạy được cho cả Bootstrap 4 và 5)
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('openFilterModalBtn');
                if (!btn) return;

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var modalEl = document.getElementById('filterModal');
                    if (!modalEl) return;

                    try {
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    } catch (err) {
                        if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    }
                });
            });
        })();
    </script>
@endpush

@endsection