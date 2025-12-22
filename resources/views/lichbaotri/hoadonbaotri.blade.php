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
                        <!-- @if($h->trang_thai_thanh_toan != 'Đã thanh toán')
                        <button class="btn btn-sm btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#paymentModal"
                            data-id="{{ $h->id }}"
                            data-route="{{ route('hoadonbaotri.update', $h->id) }}"
                            data-amount="{{ $h->chi_phi }}"
                            data-phuongthuc="{{ $h->phuong_thuc_thanh_toan }}">
                            <i class="fa fa-edit"></i> Thanh toán
                        </button>
                        @endif -->

                        <button class="btn btn-sm btn-info"
                            data-bs-toggle="modal"
                            data-bs-target="#detailModal"
                            data-mats="{{ $h->lichBaoTri->taiSan->khoTaiSan->ma_tai_san ?? '---' }}"
                            data-tents="{{ $h->lichBaoTri->taiSan->ten_tai_san ?? '---' }}"
                            data-amount="{{ number_format($h->chi_phi,0,',','.') }}"
                            data-status="{{ $h->trang_thai_thanh_toan }}"
                            data-method="{{ $h->phuong_thuc_thanh_toan ?? '---' }}"
                            data-date="{{ $h->created_at->format('d/m/Y H:i') }}"
                            data-image="{{ $h->anh_minh_chung ? asset('storage/'.$h->anh_minh_chung) : '' }}">
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
            <form id="paymentForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paymentModalLabel">💳 Cập nhật thanh toán hóa đơn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded" style="background:#e9f2ff;border:1px solid #c8ddff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-bold text-primary">
                                <i class="fa fa-credit-card me-2"></i> Số tiền
                            </div>
                            <div id="paymentAmount" class="fw-bold text-danger fs-4">0 VND</div>
                        </div>
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
                                    class="img-fluid rounded border" style="max-width: 150px;">
                                <p class="mt-2 text-muted" style="font-size: 0.85rem;">Quét mã để chuyển khoản</p>
                            </div>
                        </div>
                    </div>
                    <div id="proofImage"
                        style="display:none; margin-top:15px;">
                        <label class="form-label fw-bold">
                            📷 Ảnh minh chứng chuyển khoản
                        </label>
                        <input type="file"
                            name="anh_minh_chung"
                            class="form-control"
                            accept="image/*">
                        <small class="text-muted">
                            (JPG, PNG – tối đa 2MB)
                        </small>
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
<!-- Modal Xem Chi Tiết -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg rounded-4">

            <div class="modal-header bg-info text-white rounded-top-4">
                <h5 class="modal-title">
                    📋 Chi tiết hóa đơn bảo trì
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body px-4 py-3">

                <!-- ICON -->
                <div class="text-center mb-4">
                    <i class="fa fa-file-invoice-dollar text-info" style="font-size:50px;"></i>
                </div>

                <!-- THÔNG TIN -->
                <div class="list-group list-group-flush mb-4">

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>📌 Mã tài sản</strong>
                        <span id="detailMaTS"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>🏷 Tên tài sản</strong>
                        <span id="detailTS"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>💰 Chi phí</strong>
                        <span id="detailAmount" class="fw-bold text-danger"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>📌 Trạng thái</strong>
                        <span id="detailStatus"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>💳 Thanh toán</strong>
                        <span id="detailMethod"></span>
                    </div>

                    <div class="list-group-item d-flex justify-content-between">
                        <strong>📅 Ngày tạo</strong>
                        <span id="detailDate"></span>
                    </div>

                </div>

                <!-- ẢNH MINH CHỨNG -->
                <div id="detailImageWrapper" class="text-center" style="display:none;">
                    <h6 class="mb-3 fw-bold">📷 Ảnh minh chứng chuyển khoản</h6>
                    <img id="detailImage"
                        src=""
                        class="img-fluid rounded shadow border"
                        style="max-height:300px; cursor:zoom-in;"
                        onclick="window.open(this.src,'_blank')">
                    <p class="text-muted mt-2" style="font-size:0.85rem;">
                        Click vào ảnh để xem lớn
                    </p>
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    Đóng
                </button>
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

            document.getElementById('detailMaTS').textContent = button.dataset.mats;
            document.getElementById('detailTS').textContent = button.dataset.tents;
            document.getElementById('detailAmount').textContent =
                button.dataset.amount + ' VND';

            const status = button.dataset.status;
            document.getElementById('detailStatus').innerHTML =
                status === 'Đã thanh toán' ?
                '<span class="badge bg-success">Đã thanh toán</span>' :
                '<span class="badge bg-warning text-dark">Chưa thanh toán</span>';

            document.getElementById('detailMethod').textContent = button.dataset.method;
            document.getElementById('detailDate').textContent = button.dataset.date;

            // ẢNH MINH CHỨNG
            const image = button.dataset.image;
            const imageWrapper = document.getElementById('detailImageWrapper');
            const imageTag = document.getElementById('detailImage');

            if (image) {
                imageTag.src = image;
                imageWrapper.style.display = 'block';
            } else {
                imageWrapper.style.display = 'none';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const paymentModal = document.getElementById('paymentModal');
        const paymentForm = document.getElementById('paymentForm');
        const paymentAmount = document.getElementById('paymentAmount');
        const paymentMethod = document.getElementById('paymentMethod');
        const bankInfo = document.getElementById('bankInfo');
        const proofImage = document.getElementById('proofImage');
        const ghiChu = document.getElementById('ghi_chu');

        // Hiển thị bank info khi chọn chuyển khoản
        paymentMethod.addEventListener('change', function() {
            const isTransfer = this.value === 'Chuyển khoản';
            bankInfo.style.display = isTransfer ? 'block' : 'none';
            proofImage.style.display = isTransfer ? 'block' : 'none';
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
            const isTransfer = phuongThuc === 'Chuyển khoản';
            bankInfo.style.display = isTransfer ? 'block' : 'none';
            proofImage.style.display = isTransfer ? 'block' : 'none';

            // Xóa ghi chú
            ghiChu.value = '';
        });
    });
</script>
@endpush

@endsection