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
      z-index: 9999;
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

    /* Fix dropdown bị che khuất */
    .listing-table-wrapper {
      overflow: visible !important;
    }
    
    .table-responsive {
      overflow: visible !important;
    }
    
    .listing-table tbody tr {
      position: relative;
    }
    
    .listing-table tbody tr:has(.dropdown-menu.show) {
      z-index: 100;
    }
    
    .action-cell {
      overflow: visible !important;
    }
    
    .listing-table tbody tr.dropdown-active {
      z-index: 100;
      position: relative;
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

    .filter-card label.form-label {
      display: block;
      font-weight: 600;
      color: #333;
      font-size: .9rem;
      line-height: 1.3;
      height: auto !important;
      margin: 0 0 .35rem 0;
      padding-top: 2px;
      overflow: visible !important;
      white-space: normal !important;
    }

    /* Đảm bảo các ô lọc (select, input, nút) cao bằng nhau và text không bị cắt */
    .filter-card .form-select,
    .filter-card select.form-control,
    .filter-card input.form-control,
    .filter-card .btn {
      height: 42px;
      padding-top: 8px;
      padding-bottom: 8px;
      line-height: 1.4;
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


  <h4 class="page-title mb-0"><i class="fa fa-calendar-check-o me-2"></i> Danh sách lịch bảo trì</h4>
  <p class="text-muted mb-0">Theo dõi và tổ chức lịch bảo trì tài sản.</p>
  <div class="d-flex gap-2 mb-4">
    <a href="{{ route('lichbaotri.create') }}" class="btn btn-dergin btn-dergin--info">
      <i class="fa fa-plus"></i><span>Lên lịch mới</span>
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

      <div class="col-md-3">
        <label class="form-label d-block">&nbsp;</label>
        <div class="d-flex gap-2 filter-btns">
          <button type="submit" class="btn btn-outline-primary flex-fill">
          <i class="fa fa-filter"></i> Lọc
        </button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-outline-secondary flex-fill">
          <i class="fa fa-rotate-left"></i> Đặt lại
        </a>
        </div>
      </div>
    </form>
  </div>
  <div class="row mb-4 g-3">
    <div class="col-md-3 col-lg-2">
      <div class="card shadow-sm border-start border-info border-4 h-100">
        <div class="card-body d-flex align-items-center">
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
    <div class="col-md-3 col-lg-2">
      <div class="card shadow-sm border-start border-warning border-4 h-100">
        <div class="card-body d-flex align-items-center">
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
    <div class="col-md-3 col-lg-2">
      <div class="card shadow-sm border-start border-danger border-4 h-100">
        <div class="card-body d-flex align-items-center">
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
    <div class="col-md-3 col-lg-2">
      <div class="card shadow-sm border-start border-success border-4 h-100">
        <div class="card-body d-flex align-items-center">
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
    <div class="col-md-3 col-lg-2">
      <div class="card shadow-sm border-start border-primary border-4 h-100">
        <div class="card-body d-flex align-items-center">
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
                @if($l->trang_thai == 'Hoàn thành') bg-success text-white
                @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
                @elseif($l->trang_thai == 'Đang lên lịch') bg-info text-white
                @elseif($l->trang_thai == 'Chờ bảo trì') bg-primary text-white
                @elseif($l->trang_thai == 'Chờ thanh toán') bg-danger text-white
                @elseif($l->trang_thai == 'Đã thanh toán') bg-success text-white
                @else bg-secondary text-white @endif">
                {{ $l->trang_thai ?? 'Chưa xác định' }}
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
                  @if($l->trang_thai == 'Đang lên lịch')
                  <li>
                    <form action="{{ route('lichbaotri.tiepnhan', $l->id) }}" method="POST" class="d-inline">
                      @csrf
                      <button type="submit" class="dropdown-item text-primary" onclick="return confirm('Bạn có chắc muốn tiếp nhận báo hỏng này?')">
                        <i class="fa fa-check"></i>
                        <span>Tiếp nhận</span>
                      </button>
                    </form>
                  </li>
                  <li>
                    {{-- <button type="button"
                      class="dropdown-item text-danger btn-tuchoi"
                      data-toggle="modal"
                      data-target="#tuChoiModal"
                      data-id="{{ $l->id }}">
                      <i class="fa fa-times"></i>
                      <span>Từ chối</span>
                    </button> --}}
                  </li>
                  @else
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
                  <li>
                    <button type="button"
                      class="dropdown-item text-info"
                      data-toggle="modal"
                      data-target="#xemChiTietModal"
                      data-id="{{ $l->id }}">
                      <i class="fa fa-eye"></i>
                      <span>Xem chi tiết</span>
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
            <label for="ngay_hoan_thanh" class="form-label fw-semibold">Ngày hoàn thành</label>
            <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh"
              class="form-control" required>
          </div>

          {{-- Chi phí bảo trì --}}
          <div class="mb-3">
            <label for="chi_phi" class="form-label fw-semibold">Chi phí bảo trì (VNĐ)</label>
            <input type="number" name="chi_phi" id="chi_phi" class="form-control"
              min="0" step="1000" placeholder="Nhập chi phí..." required>
          </div>

          {{-- Ảnh sau bảo trì --}}
          <div class="mb-3">
            <label for="hinh_anh" class="form-label fw-semibold">Ảnh sau bảo trì</label>
            <input type="file" name="hinh_anh" id="hinh_anh"
              class="form-control" accept="image/*">
          </div>

          {{-- Mô tả sau bảo trì --}}
          <div class="mb-3">
            <label for="mo_ta_sau" class="form-label fw-semibold">Mô tả sau bảo trì</label>
            <textarea name="mo_ta_sau" id="mo_ta_sau" rows="3"
              class="form-control"
              placeholder="Nhập mô tả tình trạng sau khi bảo trì..." required></textarea>
          </div>

          {{-- Checkbox KTX thanh toán --}}
          <div class="mb-3">
            <div class="form-check">
              <input type="checkbox" name="ktx_thanh_toan" id="ktx_thanh_toan" class="form-check-input" value="1">
              <label for="ktx_thanh_toan" class="form-check-label fw-semibold">
                <i class="fa fa-building text-primary"></i> KTX thanh toán (không yêu cầu sinh viên trả)
              </label>
            </div>
            <small class="text-muted">Tích nếu chi phí này do ký túc xá chi trả, không cần sinh viên thanh toán.</small>
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

{{-- ❌ Modal Từ Chối --}}
<div class="modal fade" id="tuChoiModal" tabindex="-1" aria-labelledby="tuChoiLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow-lg border-0 rounded-3">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="tuChoiLabel">❌ Từ chối lịch bảo trì</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="tuChoiForm" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="tuchoi_id">

          <div class="mb-3">
            <label class="form-label fw-semibold">Lý do từ chối</label>
            <textarea name="ly_do" id="ly_do" rows="4" class="form-control"
              placeholder="Nhập lý do từ chối..." required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fa fa-times"></i> Đóng
          </button>
          <button type="submit" class="btn btn-danger">
            <i class="fa fa-paper-plane"></i> Xác nhận từ chối
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
        
        // Đóng tất cả dropdown và xóa class active
        $('.action-menu .dropdown-menu').removeClass('show');
        $('.listing-table tbody tr').removeClass('dropdown-active');
        
        if (!isOpen) {
          $menu.addClass('show');
          // Thêm class cho row để tăng z-index
          $wrapper.closest('tr').addClass('dropdown-active');
        }
        return;
      }

      if (!$target.closest('.action-menu .dropdown-menu').length) {
        $('.action-menu .dropdown-menu').removeClass('show');
        $('.listing-table tbody tr').removeClass('dropdown-active');
      }
    });

    $(document).on('click', '.action-menu .dropdown-item', function() {
      $('.action-menu .dropdown-menu').removeClass('show');
      $('.listing-table tbody tr').removeClass('dropdown-active');
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
    // ❌ Modal Từ Chối
    $('#tuChoiModal').on('show.bs.modal', function(event) {
      var button = $(event.relatedTarget);
      var id = button.data('id');

      var form = $('#tuChoiForm');
      var actionUrl = "{{ route('lichbaotri.tuchoi', ['id' => 'ID_PLACEHOLDER'], false) }}"
        .replace('ID_PLACEHOLDER', id);

      form.attr('action', actionUrl);
      $('#tuchoi_id').val(id);
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