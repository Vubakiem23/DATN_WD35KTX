@extends('admin.layouts.admin')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS Bundle (gồm Popper.js) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@section('content')
<div class="container py-4">

    <h2 class="mb-4">Danh sách hóa đơn</h2>

    <style>
        .room-actions{display:flex;gap:.5rem;flex-wrap:wrap}
        .room-actions .btn-dergin{min-width:92px}
        .room-actions .btn-dergin span{line-height:1;white-space:nowrap}
        .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease;text-decoration:none}
        .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
        .btn-dergin i{font-size:.8rem}
        .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
        .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
        .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
    </style>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex gap-3 mb-1 align-items-center">
        {{-- Nhập từ Excel --}}
        <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="file" name="file" class="form-control form-control" required style="width: auto;">
            <button type="submit" class="btn btn-dergin btn-dergin--info" title="Nhập Excel" style="margin-left: 20px;">
                <i class="fa fa-download"></i><span>Nhập Excel</span>
            </button>
        </form>

        {{-- Xuất Excel --}}
        <form method="GET" action="{{ route('hoadon.export') }}" class="d-flex align-items-center">
            <input type="hidden" name="trang_thai" value="{{ request('trang_thai') }}">
            <button type="submit" class="btn btn-dergin" title="Xuất Excel">
                <i class="fa fa-upload"></i><span>Xuất Excel</span>
            </button>
        </form>
        <a href="{{ route('hoadon.lichsu') }}" class="btn btn-dergin btn-dergin--muted" title="Lịch sử">
            <i class="fa fa-history"></i><span>Lịch sử</span>
        </a>
        <button type="button" class="btn btn-dergin btn-dergin--info" title="Bộ lọc" data-bs-toggle="modal" data-bs-target="#filterModal">
            <i class="fa fa-filter"></i><span>Lọc</span>
        </button>
    </div>
    
    <form method="POST" action="{{ route('hoadon.guiemailhangloat') }}">
    @csrf
    <button type="submit" class="btn btn-dergin btn-dergin--info mb-3" onclick="return confirm('Gửi email cho tất cả sinh viên chưa thanh toán?')">
        <i class="fa fa-envelope"></i><span>Gửi hóa đơn</span>
    </button>
</form>
    <table class="table table-bordered table-sm text-center align-middle table-hover">
        <thead class="">
            <tr class="text-center">
                <th>Khu</th>
                <th>Phòng</th>
                <th>Loại phòng</th>
                <th>Thành tiền</th>
                <th>Trạng thái</th>
                <th> 🔧Thao Tác</th>
            </tr>
        </thead>
       <tbody>
    @foreach($hoaDons as $hoaDon)
        <tr >
            <td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td>
            <td>{{ optional($hoaDon->phong)->ten_phong ?? 'Không rõ' }}</td>
            <td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td>
            <td>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }}</td>
            <td class="{{ $hoaDon->trang_thai === 'Đã thanh toán' ? 'text-success fw-bold' : 'text-danger fw-bold' }}">
              {{ $hoaDon->trang_thai ?? 'Chưa thanh toán' }}
            </td>


            <td class="text-center">
               <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')" class="d-inline">
                @csrf
                @method('DELETE')

                 <div class="room-actions justify-content-center">
                   <a href="{{ route('hoadon.show', $hoaDon->id) }}" class="btn btn-dergin btn-dergin--muted" title="Chi tiết">
                     <i class="fa fa-eye"></i><span>Chi tiết</span>
                   </a>
                   @if($hoaDon->trang_thai !== 'Đã thanh toán')
                    <button type="button"
                            class="btn btn-dergin btn-dergin--info"
                            title="Sửa giá điện/nước"
                            data-bs-toggle="modal"
                            data-bs-target="#quickUpdateModal"
                            data-id="{{ $hoaDon->id }}"
                            data-url="{{ route('hoadon.quickupdate', $hoaDon->id) }}"
                            data-dien="{{ $hoaDon->don_gia_dien }}"
                            data-nuoc="{{ $hoaDon->don_gia_nuoc }}">
                      <i class="fa fa-bolt"></i><span>Giá điện/nước</span>
                    </button>
                    <a href="{{ route('hoadon.edit', $hoaDon->id) }}" class="btn btn-dergin" title="Sửa">
                       <i class="fa fa-pencil"></i><span>Sửa</span>
                     </a>
                     <button type="button"
                            class="btn btn-dergin btn-dergin--info"
                            data-bs-toggle="modal"
                            data-bs-target="#paymentModal"
                            title="Thanh toán"
                            data-id="{{ $hoaDon->id }}">
                      <i class="fa fa-credit-card"></i><span>Thanh toán</span>
                    </button>

                   @endif
                   <button class="btn btn-dergin btn-dergin--danger" type="submit" title="Xóa">
                     <i class="fa fa-trash"></i><span>Xóa</span>
                   </button>
                 </div>
              </form>
            </td>
        </tr>
    @endforeach
</tbody>
    </table>
    

</div>
  





<script>
document.addEventListener('DOMContentLoaded', function () {
  const paymentMethodSelect = document.getElementById('paymentMethod');
  const bankInfo = document.getElementById('bankInfo');
  const confirmBtn = document.getElementById('confirmPaymentBtn');
  const paymentModal = document.getElementById('paymentModal');
  const quickUpdateModal = document.getElementById('quickUpdateModal');
  const quickUpdateBtn = document.getElementById('quickUpdateBtn');

  // Hiển thị thông tin chuyển khoản nếu chọn "chuyen_khoan"
  function toggleBankInfo() {
    const method = paymentMethodSelect?.value;
    if (bankInfo) {
      bankInfo.style.display = method === 'chuyen_khoan' ? 'block' : 'none';
    }
  }

  // Gắn sự kiện thay đổi hình thức
  if (paymentMethodSelect) {
    paymentMethodSelect.addEventListener('change', toggleBankInfo);
  }

  // Gắn ID hóa đơn vào nút khi mở modal
  if (paymentModal) {
    paymentModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const hoaDonId = button?.getAttribute('data-id');
      if (confirmBtn && hoaDonId) {
        confirmBtn.setAttribute('data-id', hoaDonId);
      }
    });
  }

  // Điền dữ liệu vào modal sửa nhanh
  if (quickUpdateModal) {
    quickUpdateModal.addEventListener('show.bs.modal', function (event) {
      const button = event.relatedTarget;
      const id = button?.getAttribute('data-id') || '';
      const url = button?.getAttribute('data-url') || '';
      const dien = button?.getAttribute('data-dien') || '';
      const nuoc = button?.getAttribute('data-nuoc') || '';
      quickUpdateModal.querySelector('input[name="don_gia_dien"]').value = dien;
      quickUpdateModal.querySelector('input[name="don_gia_nuoc"]').value = nuoc;
      if (quickUpdateBtn) {
        quickUpdateBtn.setAttribute('data-id', id);
        quickUpdateBtn.setAttribute('data-url', url);
      }
    });
  }

  // Gửi yêu cầu xác nhận thanh toán
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      const hoaDonId = this?.getAttribute('data-id');
      const hinhThuc = paymentMethodSelect?.value || '';
      const ghiChu = document.querySelector('textarea[name="ghi_chu_thanh_toan"]')?.value || '';

      if (!hoaDonId || !hinhThuc) {
        alert('⚠️ Vui lòng chọn hình thức thanh toán!');
        return;
      }

      fetch(`/hoadon/thanhtoan/${hoaDonId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          hinh_thuc_thanh_toan: hinhThuc,
          ghi_chu_thanh_toan: ghiChu
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert('✅ Thanh toán thành công!');
          const modalInstance = new bootstrap.Modal(paymentModal);
          modalInstance.hide();

          setTimeout(() => location.reload(), 500);
        } else {
          alert('❌ Có lỗi xảy ra!');
        }
      })
      .catch(err => {
        console.error('Lỗi gửi yêu cầu:', err);
        alert('❌ Không thể gửi yêu cầu. Vui lòng thử lại!');
      });
    });
  }

  // Gửi cập nhật nhanh giá điện/nước
  if (quickUpdateBtn) {
    quickUpdateBtn.addEventListener('click', function () {
      const url = this.getAttribute('data-url');
      const dien = quickUpdateModal.querySelector('input[name="don_gia_dien"]').value;
      const nuoc = quickUpdateModal.querySelector('input[name="don_gia_nuoc"]').value;
      if (!url) { alert('Không xác định được URL cập nhật.'); return; }
      fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ don_gia_dien: dien, don_gia_nuoc: nuoc })
      })
      .then(r => r.json())
      .then(data => {
        if (data?.success) {
          alert('✅ Đã cập nhật giá điện/nước.');
          const modal = bootstrap.Modal.getInstance(quickUpdateModal);
          modal?.hide();
          setTimeout(() => location.reload(), 300);
        } else {
          alert('❌ Cập nhật thất bại.');
        }
      })
      .catch(() => alert('❌ Lỗi kết nối, vui lòng thử lại.'));
    });
  }
});
</script>








@endsection
<!-- CSRF token trong <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Chọn phương thức thanh toán</h5>
        <button type="button" class="btn btn-light border-0 fs-5" data-bs-dismiss="modal" aria-label="Đóng">❌</button>
      </div>
      <div class="modal-body">
        <select id="paymentMethod" class="form-select">
          <option value="">-- Chọn hình thức --</option>
          <option value="tien_mat">Tiền mặt</option>
          <option value="chuyen_khoan">Chuyển khoản</option>
        </select>

        <div id="bankInfo" style="display: none; margin-top: 15px;">
          <div class="row">
            <div class="col-md-7">
              <p><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
              <p><strong>Số tài khoản:</strong> T1209666</p>
              <p><strong>Ngân hàng thụ hưởng:</strong> Techcombank - Chi nhánh Hà Nội</p>
            </div>
            <div class="col-md-5 text-center">
              <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded border" style="max-width: 100px;">
              <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
            </div>
          </div>
        </div>

        <div class="mb-3 mt-3">
          <label for="ghi_chu" class="form-label">Ghi chú thanh toán</label>
          <textarea name="ghi_chu_thanh_toan" class="form-control" rows="3" placeholder="Vui lòng nhập tên phòng-khu..." required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success" id="confirmPaymentBtn" data-id="">Xác nhận thanh toán</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal cập nhật nhanh giá điện/nước -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1" aria-labelledby="quickUpdateLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="quickUpdateLabel">Sửa nhanh giá điện / nước</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Đơn giá điện (đ/kWh)</label>
            <input type="number" name="don_gia_dien" class="form-control" min="0" step="100">
          </div>
          <div class="col-md-6">
            <label class="form-label">Đơn giá nước (đ/m³)</label>
            <input type="number" name="don_gia_nuoc" class="form-control" min="0" step="100">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" id="quickUpdateBtn" class="btn btn-success">Lưu</button>
      </div>
    </div>
  </div>
</div>

{{-- modal bộ lọc --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="filterModalLabel">Bộ lọc hóa đơn</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form method="GET" action="{{ route('hoadon.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
          <div>
            <label for="from_date" class="form-label">Từ ngày</label>
            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
          </div>
          <div>
            <label for="to_date" class="form-label">Đến ngày</label>
            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
          </div>
          <div>
            <label for="gia_phong_min" class="form-label">Giá phòng từ</label>
            <input type="number" name="gia_phong_min" id="gia_phong_min" class="form-control" value="{{ request('gia_phong_min') }}" placeholder="VD: 1000000">
          </div>
          <div>
            <label for="gia_phong_max" class="form-label">Giá phòng đến</label>
            <input type="number" name="gia_phong_max" id="gia_phong_max" class="form-control" value="{{ request('gia_phong_max') }}" placeholder="VD: 2000000">
          </div>
          <div>
            <label for="khu" class="form-label">Khu</label>
            <select name="khu" id="khu" class="form-select">
              <option value="">-- Tất cả --</option>
              @foreach(['A','B','C','D','E','F'] as $khu)
                <option value="{{ $khu }}" {{ request('khu') == $khu ? 'selected' : '' }}>Khu {{ $khu }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="phong_id" class="form-label">Phòng</label>
            <select name="phong_id" id="phong_id" class="form-select">
              <option value="">-- Tất cả --</option>
              @foreach($dsPhongs as $phong)
                <option value="{{ $phong->id }}" {{ request('phong_id') == $phong->id ? 'selected' : '' }}>
                  {{ $phong->ten_phong }} (ID: {{ $phong->id }})
                </option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="trang_thai" class="form-label">Trạng thái</label>
            <select name="trang_thai" id="trang_thai" class="form-select">
              <option value="">-- Tất cả --</option>
              <option value="da_thanh_toan" {{ request('trang_thai') == 'da_thanh_toan' ? 'selected' : '' }}>Đã thanh toán</option>
              <option value="chua_thanh_toan" {{ request('trang_thai') == 'chua_thanh_toan' ? 'selected' : '' }}>Chưa thanh toán</option>
            </select>
          </div>
          <div class="mt-4">
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="{{ route('hoadon.index') }}" class="btn btn-secondary ms-2">Đặt lại</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
