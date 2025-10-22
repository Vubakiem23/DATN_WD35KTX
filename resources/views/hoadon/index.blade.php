@extends('admin.layouts.admin')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách hóa đơn điện nước</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Nhập từ Excel</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead class="">
            <tr>
                <th>Phòng</th>
                <th>Điện cũ</th>
                <th>Điện mới</th>
                <th>Số điện đã dùng</th>
                <th>Nước cũ</th>
                <th>Nước mới</th>
                <th>Số nước đã dùng</th>
                <th>Đơn giá điện</th>
                <th>Đơn giá nước</th>
                <th>Thành tiền</th>
                <th> 🔧 Thao Tác</th>
            </tr>
        </thead>
       <tbody>
    @foreach($hoaDons as $hoaDon)
        <tr>
            <td>{{ $hoaDon->phong->ten_phong ?? 'Không rõ' }}</td>
            <td>{{ $hoaDon->so_dien_cu }}</td>
            <td>{{ $hoaDon->so_dien_moi }}</td>
            <td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td>
            <td>{{ $hoaDon->so_nuoc_cu }}</td>
            <td>{{ $hoaDon->so_nuoc_moi }}</td>
            <td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td>
            <td>{{ number_format($hoaDon->don_gia_dien, 0, ',', '.') }} VND</td>
            <td>{{ number_format($hoaDon->don_gia_nuoc, 0, ',', '.') }} VND</td>
            <td>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</td>

           <td> <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger ">
        ❌ Xóa
    </button><a href="{{ route('hoadon.export_pdf', $hoaDon->id) }}" class="btn btn-primary ">
    📄 Xuất PDF
</a>
<a href="{{ route('hoadon.export_excel_phong', $hoaDon->id) }}" 
   class="btn btn-success ">
    📊 Xuất Excel
</a>
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">
  Thanh toán
</button>
</form>
<td>
        </tr>
    @endforeach
</tbody>
    </table>
</div>






@endsection
<!-- Modal popup -->
<!-- Modal popup căn giữa và vừa phải -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md"> <!-- căn giữa và vừa -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">Chọn phương thức thanh toán</h5>
        <button type="button" class="btn btn-light border-0 fs-5" data-bs-dismiss="modal" aria-label="Đóng">❌</button>

      </div>
      <div class="modal-body">
        <select id="paymentMethod" class="form-select" onchange="toggleBankInfo()">
          <option value="cash">Tiền mặt</option>
          <option value="bank">Chuyển khoản</option>
        </select>

        <div id="bankInfo" style="display: none; margin-top: 15px;">
  <div class="row">
    <div class="col-md-7">
      <p><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
      <p><strong>Số tài khoản:</strong> T1209666</p>
      <p><strong>Ngân hàng thụ hưởng:</strong> Techcombank - Chi nhánh Hà Nội</p>
    </div>
    <div class="col-md-5 text-center">
      <img src="{{ asset('images/maqr.jpg') }}" alt="QR chuyển khoản" class="img-fluid rounded border" style="max-width: 100px;">
      <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
    </div>
  </div>
</div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-success">Xác nhận thanh toán</button>
      </div>
    </div>
  </div>
</div>


<script>
  function toggleBankInfo() {
    const method = document.getElementById('paymentMethod').value;
    const bankInfo = document.getElementById('bankInfo');
    bankInfo.style.display = method === 'bank' ? 'block' : 'none';
  }
</script>
