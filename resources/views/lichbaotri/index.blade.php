@extends('admin.layouts.admin')

@section('title', 'Danh sách lịch bảo trì')

@section('content')
<div class="container mt-4">
  @push('styles')
  <style>
    .page-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: #1f2937
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

    .btn-dergin--success {
      background: linear-gradient(135deg, #10b981 0%, #22c55e 100%)
    }

    .listing-table-wrapper {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
      padding: 1.25rem
    }

    .listing-table {
      margin-bottom: 0;
      border-collapse: collapse !important;
      border-spacing: 0 !important
    }

    .listing-table thead th {
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #6c757d;
      border: none;
      padding-bottom: .75rem
    }

    .listing-table tbody tr {
      background: #f9fafc;
      border-radius: 16px;
      transition: transform .2s ease, box-shadow .2s ease
    }

    .listing-table tbody tr:hover {
      /* transform: translateY(-2px); */
      box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08)
    }

    .listing-table tbody td {
      border: none;
      vertical-align: middle;
      padding: 1rem .95rem
    }

    .listing-table tbody tr td:first-child {
      border-top-left-radius: 16px;
      border-bottom-left-radius: 16px
    }

    .listing-table tbody tr td:last-child {
      border-top-right-radius: 16px;
      border-bottom-right-radius: 16px
    }

    .action-cell {
      position: relative;
      text-align: right;
      white-space: nowrap;
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
      top: 50% !important;
      right: 110%;
      left: auto;
      transform: translateY(-50%);
      z-index: 1050;
      min-width: 190px;
      border-radius: 16px;
      padding: .4rem 0;
      margin: 0;
      border: 1px solid #e5e7eb;
      box-shadow: 0 16px 40px rgba(15, 23, 42, .18);
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

    /* 🎨 Form lọc */
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


  <h4 class="page-title mb-0"> Danh sách lịch bảo trì</h4>
  <p class="text-muted mb-0">Theo dõi và tổ chức lịch bảo trì tài sản.</p>
<div class="mb-4">
  <a href="{{ route('lichbaotri.create') }}" class="btn-dergin btn-dergin--info">
    <i class="fa fa-plus-circle"></i><span>Lên lịch mới</span>
  </a>
</div>

  {{-- 🟢 Thông báo --}}
  @if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
  @endif






  {{-- 🎯 Bộ lọc khác --}}
  <div class="filter-card mb-4">
    <form method="GET" action="{{ route('lichbaotri.index') }}" class="row g-3 align-items-end">
      {{-- Giữ lại tháng/năm từ bộ lọc trên --}}
      @if(request('month'))
      <input type="hidden" name="month" value="{{ request('month') }}">
      @endif
      @if(request('year'))
      <input type="hidden" name="year" value="{{ request('year') }}">
      @endif

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-circle-check text-primary"></i> Trạng thái</label>
        <select name="trang_thai" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          <option value="Đang lên lịch" {{ request('trang_thai') == 'Đang lên lịch' ? 'selected' : '' }}>Đang lên lịch</option>
          <option value="Chờ bảo trì" {{ request('trang_thai') == 'Chờ bảo trì' ? 'selected' : '' }}>Chờ bảo trì</option>
          <option value="Đang bảo trì" {{ request('trang_thai') == 'Đang bảo trì' ? 'selected' : '' }}>Đang bảo trì</option>
          <option value="Hoàn thành" {{ request('trang_thai') == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
        </select>
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-calendar text-primary"></i> Ngày bảo trì</label>
        <input type="date" name="ngay_bao_tri" value="{{ request('ngay_bao_tri') }}" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label"><i class="fa fa-location-dot text-primary"></i> Vị trí</label>
        <select name="vi_tri" class="form-select form-control">
          <option value="">-- Tất cả --</option>
          <option value="phong" {{ request('vi_tri') == 'phong' ? 'selected' : '' }}>Phòng</option>
          <option value="kho" {{ request('vi_tri') == 'kho' ? 'selected' : '' }}>Kho</option>
        </select>
      </div>

      <div class="col-md-3 d-flex gap-2 filter-btns">
        <button type="submit" class="btn btn-success flex-fill">
          <i class="fa fa-filter"></i> Lọc
        </button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-outline-secondary flex-fill">
          <i class="fa fa-rotate-left"></i> Đặt lại
        </a>
      </div>
    </form>
  </div>
  <div class="row mb-4">
    <div class="col-md-2">
      <div class="card shadow-sm border-start border-info border-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Đang lên lịch</h6>
              <h3 class="mb-0 text-info">{{ $thongKe['dang_len_lich'] ?? 0 }}</h3>
            </div>
            <div class="text-info" style="font-size: 2rem;">
              <i class="fa fa-calendar-plus"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card shadow-sm border-start border-warning border-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Chờ bảo trì</h6>
              <h3 class="mb-0 text-warning">{{ $thongKe['cho_bao_tri'] ?? 0 }}</h3>
            </div>
            <div class="text-warning" style="font-size: 2rem;">
              <i class="fa fa-clock"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card shadow-sm border-start border-danger border-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Đang bảo trì</h6>
              <h3 class="mb-0 text-danger">{{ $thongKe['dang_bao_tri'] ?? 0 }}</h3>
            </div>
            <div class="text-danger" style="font-size: 2rem;">
              <i class="fa fa-tools"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card shadow-sm border-start border-success border-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Hoàn thành</h6>
              <h3 class="mb-0 text-success">{{ $thongKe['hoan_thanh'] ?? 0 }}</h3>
            </div>
            <div class="text-success" style="font-size: 2rem;">
              <i class="fa fa-check-circle"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card shadow-sm border-start border-primary border-4">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="text-muted mb-1">Tổng tài sản</h6>
              <h3 class="mb-0 text-primary">{{ $thongKe['tong_tai_san'] ?? 0 }}</h3>
            </div>
            <div class="text-primary" style="font-size: 2rem;">
              <i class="fa fa-boxes"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  {{-- 🧾 Bảng danh sách --}}
  <div class="listing-table-wrapper">
    <div class="table-responsive">
      <table class="table mb-0 align-middle listing-table">
        <thead>
          <tr class="text-center">
            <th>#</th>
            <th>Ảnh minh chứng</th>
            <th>Tài sản</th>
            <th>Vị trí</th>
            <th>Ngày bảo trì</th>
            <th>Ngày hoàn thành</th>
            <th>Trạng thái</th>
            <th>Mô tả</th>
            <th class="text-end" style="width: 200px;">Hành động</th>
          </tr>
        </thead>

        <tbody>
          @forelse($lich as $index => $l)
          <tr class="{{ $l->trang_thai == 'Hoàn thành' ? 'table-success' : '' }}">
            <td class="text-center">
              {{ ($lich instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $lich->firstItem() + $index : $index + 1 }}
            </td>

            {{-- Ảnh minh chứng --}}
            <td class="text-center">
              @if($l->trang_thai == 'Hoàn thành')
              @if($l->hinh_anh)
              <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh) }}"
                alt="Ảnh sau bảo trì"
                style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
              @else
              <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                style="width:70px;height:70px;">-</div>
              @endif
              @else
              @if($l->hinh_anh_truoc)
              <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh_truoc) }}"
                alt="Ảnh trước bảo trì"
                style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
              @else
              <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                style="width:70px;height:70px;">-</div>
              @endif
              @endif
            </td>

            <td>{{ $l->taiSan->ten_tai_san ?? $l->khoTaiSan->ten_tai_san ?? 'Không xác định' }}</td>
            <td>{{ $l->taiSan->phong->ten_phong ?? ($l->khoTaiSan ? 'Kho' : '-') }}</td>
            <td class="text-center">{{ $l->ngay_bao_tri }}</td>
            <td class="text-center">{{ $l->ngay_hoan_thanh ?? '-' }}</td>

            <td class="text-center">
              <span class="badge 
                @if($l->trang_thai == 'Hoàn thành') bg-success
                @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
                @elseif($l->trang_thai == 'Đang lên lịch') bg-info text-white
                @else bg-secondary @endif">
                {{ $l->trang_thai }}
              </span>
            </td>

            <td>
              @if($l->mo_ta)
              <div><strong>Trước:</strong> {{ Str::limit($l->mo_ta, 50) }}</div>
              @endif
              @if($l->mo_ta_sau)
              <div><strong>Sau:</strong> {{ Str::limit($l->mo_ta_sau, 50) }}</div>
              @endif
            </td>



            {{-- 🔧 Hành động --}}
            <td class="text-end action-cell">
              <div class="action-menu dropdown position-relative">
                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear" title="Tác vụ">
                  <i class="fa fa-gear"></i>
                </button>
                <ul class="dropdown-menu">
                  <li>
                    <button type="button"
                      class="dropdown-item"
                      data-toggle="modal"
                      data-target="#xemChiTietModal"
                      data-id="{{ $l->id }}">
                      <i class="fa fa-eye text-info"></i>
                      <span>Xem</span>
                    </button>
                  </li>
                  @if($l->trang_thai == 'Đang lên lịch')
                  <li>
                    <form action="{{ route('lichbaotri.tiepnhan', $l->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="dropdown-item text-primary" onclick="return confirm('Bạn có chắc muốn tiếp nhận báo hỏng này?')">
                        <i class="fa fa-hand-paper"></i>
                        <span>Tiếp nhận</span>
                      </button>
                    </form>
                  </li>
                  @endif
                  <li>
                    <a href="{{ route('lichbaotri.edit', $l->id) }}" class="dropdown-item">
                      <i class="fa fa-pencil text-primary"></i>
                      <span>Sửa</span>
                    </a>
                  </li>
                  <li>
                    <button type="button"
                      class="dropdown-item text-danger btn-delete-lich"
                      data-form-id="delete-lich-{{ $l->id }}">
                      <i class="fa fa-trash"></i>
                      <span>Xóa</span>
                    </button>
                  </li>
                  @if($l->trang_thai != 'Hoàn thành')
                  <li>
                    <button type="button"
                      class="dropdown-item text-success"
                      data-toggle="modal"
                      data-target="#hoanThanhModal"
                      data-id="{{ $l->id }}">
                      <i class="fa fa-check"></i>
                      <span>Hoàn thành</span>
                    </button>
                  </li>
                  @endif
                </ul>
              </div>
              <form id="delete-lich-{{ $l->id }}" action="{{ route('lichbaotri.destroy', $l->id) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="text-center text-muted p-4">Không có lịch bảo trì nào</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Phân trang --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $lich->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>


{{-- ✅ Modal Hoàn thành --}}
<div class="modal fade" id="hoanThanhModal" tabindex="-1" aria-labelledby="hoanThanhLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="hoanThanhLabel">✅ Cập nhật hoàn thành bảo trì</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>

      <form id="hoanThanhForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="lich_id">

          {{-- Ngày hoàn thành --}}
          <div class="mb-3">
            <label for="ngay_hoan_thanh" class="form-label fw-semibold"> Ngày hoàn thành</label>
            <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" 
                   class="form-control" required>
          </div>

          {{-- Ảnh sau bảo trì --}}
          <div class="mb-3">
            <label for="hinh_anh" class="form-label fw-semibold"> Ảnh sau bảo trì</label>
            <input type="file" name="hinh_anh" id="hinh_anh" 
                   class="form-control" accept="image/*">
          </div>

          {{-- Mô tả sau bảo trì --}}
          <div class="mb-3">
            <label for="mo_ta_sau" class="form-label fw-semibold"> Mô tả sau bảo trì</label>
            <textarea name="mo_ta_sau" id="mo_ta_sau" rows="3" 
                      class="form-control" 
                      placeholder="Nhập mô tả tình trạng sau khi bảo trì..." required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-times"></i> Đóng
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Lưu thay đổi
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


{{-- 👁️ Modal Xem Chi Tiết --}}
<div class="modal fade" id="xemChiTietModal" tabindex="-1" aria-labelledby="xemChiTietLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="xemChiTietLabel">👁️ Chi tiết bảo trì</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="chiTietContent">
        <div class="text-center text-muted py-3">Đang tải dữ liệu...</div>
      </div>
    </div>
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

    $(document).on('click', '.btn-delete-lich', function(e) {
      e.preventDefault();
      const formId = $(this).data('form-id');
      if (!formId) {
        return;
      }
      if (confirm('Bạn có chắc muốn xóa lịch này không?')) {
        const form = document.getElementById(formId);
        if (form) {
          form.submit();
        }
      }
    });

    // 🟢 Modal Hoàn thành (Bootstrap 4 - jQuery events)
    $('#hoanThanhModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var $form = $('#hoanThanhForm');
      // Dùng route relative (absolute=false) để tránh lệch domain (localhost vs 127.0.0.1)
      $form.attr('action', "{{ route('lichbaotri.hoanthanh.submit', ['id' => 'ID_PLACEHOLDER'], false) }}".replace('ID_PLACEHOLDER', id));
      $('#lich_id').val(id);
    });

    // 🔵 Modal Xem chi tiết
    $('#xemChiTietModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');
      var $content = $('#chiTietContent');
      $content.html('<div class="text-center text-muted py-3">Đang tải dữ liệu...</div>');

      $.get("{{ route('lichbaotri.show', ['id' => 'ID_PLACEHOLDER'], false) }}".replace('ID_PLACEHOLDER', id))
        .done(function(html) {
          $content.html(html);
        })
        .fail(function() {
          $content.html('<div class="text-danger text-center">Lỗi tải dữ liệu</div>');
        });
    });
  });
</script>
@endpush
@endsection