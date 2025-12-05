@extends('client.layouts.app')

@section('title', 'Báo sự cố - Sinh viên')

@section('content')
<!-- Header màu xanh đậm -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-center align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Báo sự cố phòng
        </h4>
    </div>
</div>

@if(!$sinhVien)
    {{-- Chưa nộp hồ sơ --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                    <h4 class="text-info">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                    <p class="text-muted">Vui lòng nộp hồ sơ để có thể báo sự cố.</p>
                </div>
            </div>
        </div>
    </div>

@elseif(!$phong)
    {{-- Chưa có phòng --}}
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm text-center py-5">
                <div class="card-body">
                    <i class="fas fa-door-open fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Bạn chưa được phân phòng</h4>
                    <p class="text-muted">Vui lòng liên hệ quản trị viên để được phân phòng.</p>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- Form và danh sách sự cố --}}
    <div class="row">
        {{-- Form báo sự cố --}}
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header  text-white">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-plus-circle me-2"></i> Báo cáo sự cố mới
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('client.suco.store') }}" method="POST" enctype="multipart/form-data" class="flex-fill d-flex flex-column">
                        @csrf
                        <input type="hidden" name="phong_id" value="{{ $phong->id }}">

                        <div class="mb-3">
                            <label class="form-label">Sinh viên</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $sinhVien->ho_ten }} ({{ $sinhVien->ma_sinh_vien }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phòng</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $phong->ten_phong }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả sự cố</label>
                            <textarea name="mo_ta" class="form-control" rows="4" 
                                      placeholder="Nhập mô tả chi tiết..." required>{{ old('mo_ta') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh minh chứng (nếu có)</label>
                            <input type="file" name="anh" class="form-control" accept="image/*">
                        </div>

                        <div class="mt-auto">
                            <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm" style="padding: 12px; font-size: 16px; border-radius: 10px;">
                                <i class="fa fa-paper-plane me-2"></i> Gửi báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Danh sách sự cố --}}
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i> Sự cố gần đây</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:600px; overflow:auto;">
                        @if($dsSuCo->count())
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light position-sticky top-0" style="z-index:1;">
                                    <tr>
                                        <th class="fit text-center">ID</th>
                                        <th class="fit">Ảnh</th>
                                        <th class="fit">Ngày gửi</th>
                                        <th class="fit">Ngày hoàn thành</th>
                                        <th>Mô tả</th>
                                        <th class="fit">Giá tiền</th>
                                        <th class="fit">Trạng thái</th>
                                        <th class="fit">Thanh toán</th>
                                        <th class="fit">Hành động</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dsSuCo as $sc)
                                        <tr>
                                            <td class="text-center">{{ $sc->id }}</td>
                                            <td>
                                                <img src="{{ $sc->display_anh }}" 
                                                     alt="Ảnh sự cố" 
                                                     class="img-thumbnail" 
                                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                            </td>
                                            <td>{{ $sc->ngay_gui?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $sc->ngay_hoan_thanh?->format('d/m/Y') ?? '-' }}</td>
                                            <td class="description">{{ $sc->mo_ta }}</td>

                                            <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount,0,',','.') . ' ₫' : '0 ₫' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $sc->trang_thai == 'Hoàn thành' ? 'success' : 'warning' }}">
                                                    {{ $sc->trang_thai }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($sc->payment_amount > 0)
                                                    @if (!$sc->is_paid)
                                                        <button type="button" 
                                                            class="btn btn-pay"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#paymentModal"
                                                            data-id="{{ $sc->id }}"
                                                            data-url="{{ route('client.su_co.thanhtoan', $sc->id) }}"
                                                            data-amount="{{ $sc->payment_amount }}">
                                                            <i class="fa fa-money-bill"></i> Thanh toán
                                                        </button>
                                                    @else
                                                        <span class="badge bg-success"><i class="fa fa-check-circle"></i> Đã TT</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary"><i class="fa fa-clock"></i> Chưa có giá</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
    <button type="button"
        class="btn btn-light btn-sm btn-show-detail"
        data-bs-toggle="modal"
        data-bs-target="#detailModal"

        data-id="{{ $sc->id }}"
        data-ngay-gui="{{ $sc->ngay_gui?->format('d/m/Y') }}"
        data-ngay-hoan-thanh="{{ $sc->ngay_hoan_thanh?->format('d/m/Y') ?? '-' }}"
        data-trang-thai="{{ $sc->trang_thai }}"
        data-mo-ta="{{ $sc->mo_ta }}"
        data-payment="{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount, 0, ',', '.') . ' ₫' : '0 ₫' }}"

        data-anh-cu="{{ $sc->display_anh }}"
        data-anh-moi="{{ $sc->display_anh_moi }}"
    >
        <i class="fa fa-eye text-primary fs-5"></i>
    </button>
</td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-4x mb-3" style="opacity: 0.3;"></i>
                                <div class="fst-italic">Chưa có sự cố nào</div>
                            </div>
                        @endif
                    </div>

                    @if($dsSuCo->count())
                        <div class="mt-2 d-flex justify-content-center p-2">
                            {{ $dsSuCo->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Modal thanh toán --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">💳 Thanh toán sự cố</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <strong>Số tiền:</strong> <span id="paymentAmount" class="text-danger fs-5">0 ₫</span>
                </div>
                <div class="mb-3">
                    <label for="paymentMethod" class="form-label">Chọn hình thức</label>
                    <select id="paymentMethod" class="form-select" required>
                        <option value="">-- Chọn --</option>
                        <option value="tien_mat">💵 Tiền mặt</option>
                        <option value="chuyen_khoan">🏦 Chuyển khoản</option>
                    </select>
                </div>
                <div id="bankInfo" style="display: none; margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
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
                    <label for="ghi_chu_thanh_toan" class="form-label">Ghi chú</label>
                    <textarea id="ghi_chu_thanh_toan" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button class="btn btn-success" id="confirmPaymentBtn"><i class="fa fa-check"></i> Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chi tiết Sự cố -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">

            <!-- Header -->
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-info-circle me-2"></i> Chi tiết Sự cố
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">

                <!-- Thông tin -->
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="info-box p-3 bg-light rounded border">
                            <strong class="text-secondary">Ngày gửi</strong>
                            <div id="ct-ngay-gui" class="fw-semibold mt-1"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box p-3 bg-light rounded border">
                            <strong class="text-secondary">Ngày hoàn thành</strong>
                            <div id="ct-ngay-hoan-thanh" class="fw-semibold mt-1"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="info-box p-3 bg-light rounded border">
                            <strong class="text-secondary">Trạng thái</strong>
                            <div id="ct-trang-thai" class="fw-bold mt-1"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-box p-3 bg-light rounded border">
                            <strong class="text-secondary">Thanh toán</strong>
                            <div id="ct-payment" class="fw-bold mt-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="mb-4">
                    <strong class="text-secondary d-block mb-2">Mô tả</strong>
                    <div id="ct-mo-ta" class="p-3 bg-light rounded border" style="min-height: 60px;"></div>
                </div>

                <!-- Ảnh -->
                <div class="row g-3">
                        <!-- Ảnh cũ -->
                        <div class="col-md-6">
                            <strong class="text-secondary d-block text-center">Ảnh gốc</strong>
                            <div class="modal-image-box mt-2">
                                <img id="ct-anh-cu" src="" alt="">
                            </div>
                        </div>

                        <!-- Ảnh mới -->
                        <div class="col-md-6" id="anh-moi-block">
                            <strong class="text-secondary d-block text-center">Ảnh sau sửa</strong>
                            <div class="modal-image-box mt-2">
                                <img id="ct-anh-moi" src="" alt="">
                        </div>
                </div>
            </div>
            </div>

        </div>
    </div>
</div>





@push('styles')
<style>
    /* Header */
    .page-header-dark{
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(26, 35, 126, 0.4);
        margin-bottom: 30px;
        overflow: hidden;
    }
    .card-header  {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px 25px;
        box-shadow: 0 2px 10px rgba(23, 162, 184, 0.2);
    }
    /* Thu nhỏ kích thước chữ trong bảng */
.table td, 
.table th {
    font-size: 13px;       /* chữ nhỏ gọn */
    white-space: nowrap;   /* không xuống dòng */
    vertical-align: middle; 
    padding: 6px 10px;     /* thu gọn khoảng cách */
}

/* Giới hạn mô tả tránh tràn */
.table td.description {
    max-width: 200px;
    white-space: normal;   /* riêng mô tả cho xuống dòng */
}

/* Ảnh thu nhỏ không làm tăng chiều cao dòng */
.table img {
    border-radius: 6px;
    object-fit: cover;
}
.btn-pay {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    font-size: 0.9rem;
    font-weight: 600;
    border-radius: 8px;
    color: #fff;
    background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
    border: none;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    transition: all 0.2s ease-in-out;
}

.btn-pay:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(13, 110, 253, 0.4);
    background: linear-gradient(135deg, #0056b3 0%, #0d6efd 100%);
}

.btn-pay i {
    font-size: 1rem;
}


</style>
<style>
.btn-dergin {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: .35rem;
    padding: .45rem .75rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: .85rem;
    border: none;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    box-shadow: 0 4px 12px rgba(78, 84, 200, .22);
    transition: all .2s ease;
}
.btn-dergin:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 18px rgba(78, 84, 200, .32);
}
.room-actions .dropdown-menu {
    border-radius: 12px;
    padding: .5rem 0;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
.room-actions .dropdown-item:hover {
    background-color: #eef2ff;
}
</style>
<style>
.info-box {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
    border-left: 4px solid #0d6efd;
}
.info-box label {
    font-size: .85rem;
    color: #6c757d;
    margin-bottom: 2px;
}
.info-box .value {
    font-weight: 600;
    font-size: 1rem;
}

.detail-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    border-left: 4px solid #6c757d;
}
.info-box {
    transition: 0.2s;
}

.info-box:hover {
    background: #eef4ff !important;
    border-color: #bcd0ff !important;
}
.modal-image-box {
    width: 100%;
    height: 260px; 
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden; 
    border-radius: 10px;
    border: 1px solid #ddd;
    background: #fff;
}

.modal-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* luôn cắt ảnh vừa khung */
    border-radius: 10px;
}


</style>
<style>
    .btn-show-detail {
    border: none;
    background: transparent;
    cursor: pointer;
}

.btn-show-detail:hover i {
    color: #0d6efd; /* xanh primary */
    transform: scale(1.1);
    transition: 0.2s;
}

</style>
@endpush
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".btn-show-detail").forEach(btn => {
        btn.addEventListener("click", function () {

            // Lấy data
            const ngayGui = this.dataset.ngayGui;
            const ngayHoanThanh = this.dataset.ngayHoanThanh;
            const trangThai = this.dataset.trangThai;
            const moTa = this.dataset.moTa;
            const payment = this.dataset.payment;
            const anhCu = this.dataset.anhCu;
            const anhMoi = this.dataset.anhMoi;

            // Gán vào modal
            document.getElementById("ct-ngay-gui").textContent = ngayGui;
            document.getElementById("ct-ngay-hoan-thanh").textContent = ngayHoanThanh;
            document.getElementById("ct-trang-thai").textContent = trangThai;
            document.getElementById("ct-payment").textContent = payment;
            document.getElementById("ct-mo-ta").textContent = moTa;

            // Ảnh cũ
            document.getElementById("ct-anh-cu").src = anhCu;

            // Ảnh mới
            const anhMoiBlock = document.getElementById("anh-moi-block");
            const imgAnhMoi = document.getElementById("ct-anh-moi");

            if (anhMoi && anhMoi !== "" && anhMoi !== "null") {
                imgAnhMoi.src = anhMoi;
                anhMoiBlock.style.display = "block";
            } else {
                anhMoiBlock.style.display = "none";
            }
        });
    });
});
</script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = document.getElementById('paymentModal');
    const paymentAmount = document.getElementById('paymentAmount');
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const bankInfo = document.getElementById('bankInfo');
    const ghiChuEl = document.getElementById('ghi_chu_thanh_toan');
    const confirmBtn = document.getElementById('confirmPaymentBtn');

    let actionUrl = '';

    // Khi mở modal
    paymentModal.addEventListener('show.bs.modal', function(event) {
        const btn = event.relatedTarget;
        const amount = btn.getAttribute('data-amount');
        actionUrl = btn.getAttribute('data-url');

        paymentAmount.textContent = new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
        paymentMethodSelect.value = '';
        ghiChuEl.value = '';
        bankInfo.style.display = 'none';
    });

    // Hiển thị thông tin chuyển khoản
    paymentMethodSelect.addEventListener('change', function() {
        bankInfo.style.display = this.value === 'chuyen_khoan' ? 'block' : 'none';
    });

    // Gửi yêu cầu thanh toán
    confirmBtn.addEventListener('click', function() {
        const hinhThuc = paymentMethodSelect.value;
        const ghiChu = ghiChuEl.value.trim();

        if (!hinhThuc) return alert('Chọn hình thức thanh toán!');
        if (!ghiChu) return alert('Nhập ghi chú thanh toán!');

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang xử lý...';

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                hinh_thuc_thanh_toan: hinhThuc,
                ghi_chu_thanh_toan: ghiChu
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(paymentModal).hide();
                window.location.reload();
            } else {
                alert(data.message || 'Có lỗi xảy ra!');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fa fa-check"></i> Xác nhận';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Không thể gửi yêu cầu!');
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fa fa-check"></i> Xác nhận';
        });
    });
});
</script>

@endpush

@endsection
