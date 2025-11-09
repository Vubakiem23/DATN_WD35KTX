@extends('admin.layouts.admin')
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap 5 JS Bundle (bao gồm Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



@section('content')
<div class="container py-4">

    <h2 class="mb-4">Danh sách hóa đơn</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex gap-3 mb-1 align-items-center">
        {{-- Nhập từ Excel --}}
        <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center">
            @csrf
            <input type="file" name="file" class="form-control form-control" required style="width: auto;">
            <button type="submit" class="btn btn-outline-primary btn-action"" title="Nhập excel" style="margin-left: 20px;">📥</button>
        </form>

        {{-- Xuất Excel --}}
        <form method="GET" action="{{ route('hoadon.export') }}" class="d-flex align-items-center">
            <input type="hidden" name="trang_thai" value="{{ request('trang_thai') }}">
            <button type="submit" class="btn btn-outline-primary btn-action"" title="Xuất excel">📤</button>
        </form>
        <a href="{{ route('hoadon.lichsu') }}" class="btn btn-outline-primary mb-3" title="Lịch sử hóa đơn">📜</a>
        <button type="button" class="btn btn-outline-primary mb-3" title="Bộ lọc" data-bs-toggle="modal" data-bs-target="#filterModal">
  🔍 
</button>
    </div>
</div>
    </form>    
    <form method="POST" action="{{ route('hoadon.guiemailhangloat') }}">
    @csrf
    <button type="submit" class="btn btn-success mb-3" onclick="return confirm('Gửi email cho tất cả sinh viên chưa thanh toán?')">
        📧 Gửi hóa đơn 
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


           <td>
              <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')" class="d-inline">
                @csrf
                @method('DELETE')

                <div class="d-flex gap-1 justify-content-center flex-nowrap">
                  <button class="btn btn-outline-danger btn-action" type="submit" title="Xóa"><i class="fa fa-trash"></i></button>
                  @if($hoaDon->trang_thai !== 'Đã thanh toán')
                    <button type="button"class="btn btn-outline-primary btn-action"data-bs-toggle="modal"data-bs-target="#paymentModal"data-bs-toggle="tooltip"data-bs-placement="bottom"title="Thanh toán"data-id="{{ $hoaDon->id }}">💰</button>
                  @endif
                  @if($hoaDon->trang_thai !== 'Đã thanh toán')
                  <a href="{{ route('hoadon.show', $hoaDon->id) }}" class="btn btn-outline-primary btn-action"" title="Chi tiết">👁️</a>
                   @endif
                  @if($hoaDon->trang_thai !== 'Đã thanh toán')
                    <a href="{{ route('hoadon.edit', $hoaDon->id) }}"  class="btn btn-outline-primary btn-action"" title="Sửa">✏️</a>
                  @endif
                 
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
