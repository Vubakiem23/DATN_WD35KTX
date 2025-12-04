@extends('admin.layouts.admin')

@section('title', 'Danh sách hóa đơn bảo trì')

@section('content')
<div class="container mt-4">
    <h4 class="page-title mb-0">Danh sách hóa đơn bảo trì</h4>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive mt-3">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lịch bảo trì</th>
                    <th>Chi phí</th>
                    <th>Trạng thái thanh toán</th>
                    <th>Phương thức thanh toán</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($hoaDons as $index => $h)
                <tr>
                    <td>{{ $hoaDons->firstItem() + $index }}</td>
                    <td>{{ $h->lichBaoTri->taiSan->ten_tai_san ?? 'Không xác định' }}</td>
                    <td>{{ number_format($h->chi_phi,0,',','.') }} VND</td>
                    <td>
                        <span class="badge 
                        @if($h->trang_thai_thanh_toan == 'Đã thanh toán') bg-success
                        @elseif($h->trang_thai_thanh_toan == 'Chưa thanh toán') bg-warning text-dark
                        @else bg-secondary @endif">
                            {{ $h->trang_thai_thanh_toan }}
                        </span>
                    </td>
                    <td>{{ $h->phuong_thuc_thanh_toan ?? '-' }}</td>
                    <td>{{ $h->created_at->format('d/m/Y') }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#paymentModal"
                            data-id="{{ $h->id }}"
                            data-route="{{ route('hoadonbaotri.update', $h->id) }}"
                            data-amount="{{ $h->chi_phi }}"
                            data-phuongthuc="{{ $h->phuong_thuc_thanh_toan }}">
                            <i class="fa fa-edit"></i> Cập nhật
                        </button>
                        <button class="btn btn-sm btn-info"
    data-bs-toggle="modal"
    data-bs-target="#detailModal"
    data-mats="{{ $h->lichBaoTri->taiSan->khoTaiSan->ma_tai_san ?? $h->lichBaoTri->khoTaiSan->ma_tai_san ?? 'Không có' }}"
    data-tents="{{ $h->lichBaoTri->taiSan->ten_tai_san ?? $h->lichBaoTri->khoTaiSan->ten_tai_san ?? 'Không xác định' }}"
    data-amount="{{ number_format($h->chi_phi,0,',','.') }}"
    data-status="{{ $h->trang_thai_thanh_toan }}"
    data-method="{{ $h->phuong_thuc_thanh_toan ?? '---' }}"
    data-date="{{ $h->created_at->format('d/m/Y H:i') }}">
    👁 Xem
</button>



                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">Không có hóa đơn nào</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center mt-3">
            {{ $hoaDons->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- Modal thanh toán --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Cập nhật thanh toán hóa đơn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        <strong>Số tiền:</strong>
                        <span id="paymentAmount" class="text-danger fs-5">0 VND</span>
                    </div>

                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Phương thức thanh toán</label>
                        <select name="phuong_thuc_thanh_toan" id="paymentMethod" class="form-select" required>
                            <option value="">-- Chọn hình thức --</option>
                            <option value="Tiền mặt">💵 Tiền mặt</option>
                            <option value="Chuyển khoản">🏦 Chuyển khoản</option>
                        </select>
                    </div>

                    <div id="bankInfo"
                        style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                        <h6 class="mb-3">Thông tin chuyển khoản:</h6>
                        <div class="row">
                            <div class="col-md-7">
                                <p class="mb-2"><strong>Tên tài khoản:</strong> Nguyễn Quang Thắng</p>
                                <p class="mb-2"><strong>Số tài khoản:</strong> T1209666</p>
                                <p class="mb-0"><strong>Ngân hàng:</strong> Techcombank - Chi nhánh Hà Nội</p>
                            </div>
                            <div class="col-md-5 text-center">
                                <img src="{{ asset('images/ma1qr.jpg') }}" alt="QR chuyển khoản"
                                    class="img-fluid rounded border" style="max-width: 120px;">
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 mt-3">
                        <label for="ghi_chu" class="form-label">Ghi chú thanh toán</label>
                        <textarea name="ghi_chu" id="ghi_chu" class="form-control" rows="3"
                            placeholder="Nhập ghi chú thanh toán" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-success" id="confirmPaymentBtn">
                        <i class="fa fa-check"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- //modal xem chi tiết -->
{{-- Modal Xem Chi Tiết --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">📋 Chi tiết hóa đơn bảo trì</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- 🔹 Toàn bộ phần modal-body bạn đã viết đặt vào đây -->
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12 text-center">
                        <i class="fa fa-file-invoice-dollar text-info" style="font-size: 45px;"></i>
                    </div>
                </div>

                <div class="list-group">

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>📌 Mã tài sản</strong></span>
                        <span id="detailMaTS"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>🏷 Tên tài sản</strong></span>
                        <span id="detailTS"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>💰 Chi phí</strong></span>
                        <span id="detailAmount" class="text-danger fw-bold"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>📌 Trạng thái</strong></span>
                        <span id="detailStatus"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>💳 Thanh toán</strong></span>
                        <span id="detailMethod"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <span><strong>📅 Ngày tạo</strong></span>
                        <span id="detailDate"></span>
                    </div>

                </div>
            </div>
            <!-- 🔹 Kết thúc phần body -->

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>

        </div>
    </div>
</div>



<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = document.getElementById('detailModal');

        detailModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            document.getElementById('detailMaTS').textContent = button.getAttribute('data-mats');
            document.getElementById('detailTS').textContent = button.getAttribute('data-tents');
            document.getElementById('detailAmount').textContent =
                button.getAttribute('data-amount') + ' VND';

            const status = button.getAttribute('data-status');
            document.getElementById('detailStatus').innerHTML =
                status === 'Đã thanh toán' ?
                '<span class="badge bg-success">Đã thanh toán</span>' :
                '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';

            document.getElementById('detailMethod').textContent =
                button.getAttribute('data-method');

            document.getElementById('detailDate').textContent =
                button.getAttribute('data-date');
        });
    });


    document.addEventListener('DOMContentLoaded', function() {
        const paymentModal = document.getElementById('paymentModal');
        const paymentForm = document.getElementById('paymentForm');
        const paymentAmount = document.getElementById('paymentAmount');
        const paymentMethod = document.getElementById('paymentMethod');
        const bankInfo = document.getElementById('bankInfo');
        const ghiChu = document.getElementById('ghi_chu');

        // Hiển thị bank info khi chọn chuyển khoản
        paymentMethod.addEventListener('change', function() {
            bankInfo.style.display = this.value === 'Chuyển khoản' ? 'block' : 'none';
        });

        // Khi mở modal
        paymentModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const route = button.getAttribute('data-route');
            const amount = button.getAttribute('data-amount');
            const phuongThuc = button.getAttribute('data-phuongthuc');

            // Gán đúng route action
            paymentForm.action = route;

            // Hiển thị số tiền
            paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' VND';

            // Chọn phương thức thanh toán
            paymentMethod.value = phuongThuc ?? '';
            bankInfo.style.display = phuongThuc === 'Chuyển khoản' ? 'block' : 'none';

            // Xóa ghi chú
            ghiChu.value = '';
        });
    });
</script>
@endpush

@endsection