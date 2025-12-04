@extends('admin.layouts.admin')
@section('content')
<div class="container mt-4">

        <div>
            <h3 class="room-page__title mb-1"><i class="fa fa-exclamation-triangle me-2"></i> Danh sách sự cố</h3>
            <p class="text-muted mb-0">Theo dõi, xử lý và cập nhật trạng thái các sự cố ký túc xá</p>
        </div>

    <div class="d-flex gap-2">
        <a href="{{ route('suco.create') }}" class="btn btn-dergin btn-dergin--info">
            <i class="fa fa-plus"></i><span>Thêm sự cố</span>
        </a>
    </div>

    {{-- Ô tìm kiếm + lọc --}}
    <form method="GET" action="{{ route('suco.index') }}" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm MSSV, họ tên hoặc mô tả sự cố">
            <select name="trang_thai" class="form-select form-control" style="max-width:160px;">
                <option value="">Tất cả</option>
                <option value="Tiếp nhận" @selected(request('trang_thai')=='Tiếp nhận' )>Tiếp nhận</option>
                <option value="Đang xử lý" @selected(request('trang_thai')=='Đang xử lý' )>Đang xử lý</option>
                <option value="Hoàn thành" @selected(request('trang_thai')=='Hoàn thành' )>Hoàn thành</option>
            </select>
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (request()->has('search') || request()->has('trang_thai'))
            <a href="{{ route('suco.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    {{-- Thông báo --}}
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-times-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        <i class="fa fa-info-circle"></i> {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fa fa-exclamation-triangle"></i> Vui lòng kiểm tra lại thông tin:
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper">
        <div class="table-responsive">
            <table class="table table-hover mb-0 room-table">
                <thead>
                    <tr>
                        <th class="fit text-center">ID</th>
                        <th>Sinh viên</th>
                        <th class="fit">Phòng / Khu</th>
                        <th class="fit">Ngày gửi</th>
                        <th class="fit">Hoàn thành</th>
                        <th class="fit">Ảnh</th>
                        <th class="fit">Mô tả</th>
                        <th class="fit">Trạng thái</th>
                        <th class="fit">Giá tiền</th>
                        <th class="fit">Thanh toán</th>
                        <th class="text-end fit text-center">HÀNH ĐỘNG</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($su_cos as $sc)
                    @php
                    $badge = match ($sc->trang_thai) {
                    'Tiếp nhận' => 'badge-soft-secondary',
                    'Đang xử lý' => 'badge-soft-warning',
                    'Hoàn thành' => 'badge-soft-success',
                    default => 'badge-soft-secondary',
                    };
                    $student = $sc->sinhVien ?? null;
                    $phong = null;
                    if ($student) {
                    if ($student->slot && $student->slot->phong) {
                    $phong = $student->slot->phong;
                    } elseif ($student->phong) {
                    $phong = $student->phong;
                    } elseif ($sc->phong) {
                    $phong = $sc->phong;
                    }
                    } elseif ($sc->phong) {
                    $phong = $sc->phong;
                    }
                    $tenPhongDisplay = $phong->ten_phong ?? '-';
                    $tenKhuDisplay = $phong->khu->ten_khu ?? '-';
                    $anhUrl =
                    $sc->anh && file_exists(public_path($sc->anh))
                    ? asset($sc->anh)
                    : 'https://dummyimage.com/56x56/eff3f9/9aa8b8&text=IMG';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $sc->id }}</td>
                        <td>
                            <div class="fw-semibold">{{ $student->ho_ten ?? '---' }}</div>
                            <small class="text-muted">MSSV: {{ $student->ma_sinh_vien ?? '---' }}</small>
                        </td>
                        <td class="fit">
                            <div>{{ $tenPhongDisplay }}</div>
                            <small class="text-muted">Khu {{ $tenKhuDisplay }}</small>
                        </td>
                        <td>{{ $sc->ngay_gui ? \Carbon\Carbon::parse($sc->ngay_gui)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $sc->ngay_hoan_thanh ? \Carbon\Carbon::parse($sc->ngay_hoan_thanh)->format('d/m/Y') : '-' }}
                        </td>
                        <td><img src="{{ $anhUrl }}" alt="Ảnh sự cố" class="avatar-56"></td>
                        <td style="max-width:200px;">{{ $sc->mo_ta }}</td>
                        <td><span class="badge {{ $badge }}">{{ $sc->trang_thai }}</span></td>
                        <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount, 0, ',', '.') . ' ₫' : '0 ₫' }}
                        </td>
                        <td>
                            @if ($sc->payment_amount == 0)
                            <span class="badge badge-soft-secondary">Không TT</span>
                            @elseif($sc->is_paid)
                            <span class="badge badge-soft-success">Đã TT</span>
                            @else
                            <span class="badge badge-soft-warning">Chưa TT</span>
                            @endif
                        </td>
                        <td class="text-end fit action-cell">
                            <div class="room-actions dropdown position-relative">
                                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear" title="Tác vụ">
                                    <i class="fa fa-gear"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('suco.show', $sc->id) }}" class="dropdown-item">
                                            <i class="fa fa-eye text-info"></i>
                                            <span>Xem</span>
                                        </a>
                                    </li>
                                    <!-- <li>
                                        <a href="{{ route('suco.edit', $sc->id) }}" class="dropdown-item">
                                            <i class="fa fa-pencil text-primary"></i>
                                            <span>Sửa</span>
                                        </a>
                                    </li> -->
                                    @if ($sc->trang_thai == 'Tiếp nhận')
                                        <li>
                                            <form action="{{ route('suco.dangXuLy', $sc->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="fa fa-spinner"></i>
                                                    <span>Tiếp nhận</span>
                                                </button>
                                            </form>
                                        </li>
                                        @endif

                                    @if ($sc->trang_thai != 'Hoàn thành')
                                    <li>
                                        <button type="button"
                                            class="dropdown-item text-success btn-hoan-thanh"
                                            data-bs-toggle="modal"
                                            data-bs-target="#hoanThanhModal"
                                            data-id="{{ $sc->id }}"
                                            data-ngay="{{ $sc->ngay_hoan_thanh }}"
                                            data-nguoi_tao="{{ $sc->nguoi_tao }}">
                                            <i class="fa fa-check"></i>
                                            <span>Hoàn thành</span>
                                        </button>
                                    </li>
                                    @endif
                                    <li>
                                        <button type="button"
                                            class="dropdown-item text-danger btn-delete-su-co"
                                            data-form-id="delete-su-co-{{ $sc->id }}">
                                            <i class="fa fa-trash"></i>
                                            <span>Xóa</span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <form id="delete-su-co-{{ $sc->id }}" action="{{ route('suco.destroy', $sc->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">
                            <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2"
                                alt="">
                            <div>Chưa có sự cố nào</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $su_cos->onEachSide(1)->links() }}
    </div>
</div>

{{-- Modal Hoàn thành --}}
<div class="modal fade" id="hoanThanhModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">✅ Hoàn thành sự cố</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="hoanThanhForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="suco_id">
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fa fa-info-circle"></i>
                        Khi ấn Hoàn thành, bạn bắt buộc cập nhật <strong>ảnh sau xử lý</strong> và <strong>độ hoàn thiện (%)</strong>.
                        Nếu có <strong>số tiền cần thanh toán</strong>, hệ thống sẽ chuyển sang trang <strong>Hóa đơn sự cố</strong> để thực hiện thanh toán.
                    </div>
                    <div class="mb-3">
                        <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành</label>
                        <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" class="form-control"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="completion_percent" class="form-label d-flex justify-content-between">
                            <span>Độ hoàn thiện (%) <span class="text-danger">*</span></span>
                            <span id="completion_percent_value" class="fw-semibold">100%</span>
                        </label>
                        <input type="range" name="completion_percent" id="completion_percent" class="form-range" min="0" max="100" step="1" value="100" required>
                    </div>
                    <div class="mb-3">
                        <label for="anh_modal" class="form-label">Ảnh sau khi xử lý <span class="text-danger">*</span></label>
                        <input type="file" name="anh_sau" id="anh_modal" class="form-control" accept="image/*" required>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" value="1" id="co_thanh_toan" name="co_thanh_toan">
                        <label class="form-check-label" for="co_thanh_toan">
                            Có thanh toán
                        </label>
                    </div>
                    <div class="mb-3 d-none" id="payment_amount_group">
                        <label for="payment_amount" class="form-label">Giá tiền thanh toán</label>
                        <input type="number" name="payment_amount" id="payment_amount" class="form-control" min="0" step="1000" placeholder="Nhập số tiền (VNĐ)">
                        <small class="text-muted">Nếu > 0, hệ thống sẽ chuyển sang trang Hóa đơn sự cố để thanh toán.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-success">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
                html {
                    scroll-behavior: auto !important
                }


                .room-page__title {
                    font-size: 1.75rem;
                    font-weight: 700;
                    color: #1f2937;
                }

                .room-table-wrapper {
    overflow-x: auto;
}

                .room-table {
                    margin-bottom: 0;
                    border-collapse: separate;
                    border-spacing: 0 12px;
                }

                .room-table thead th {
                    font-size: .78rem;
                    text-transform: uppercase;
                    letter-spacing: .05em;
                    color: #6c757d;
                    border: none;
                    padding-bottom: .75rem;
                }

                .room-table tbody tr {
                    background: #f9fafc;
                    border-radius: 16px;
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .room-table tbody tr:hover {
                    /* transform: translateY(-2px); */
                    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
                }

                .room-table tbody td {
                    border: none;
                    vertical-align: middle;
                    padding: 1rem .95rem;
                }

                .room-table tbody tr td:first-child {
                    border-top-left-radius: 16px;
                    border-bottom-left-radius: 16px;
                }

                .room-table tbody tr td:last-child {
                    border-top-right-radius: 16px;
                    border-bottom-right-radius: 16px;
                }

                .room-table .fit {
                    white-space: nowrap;
                    width: 1%;
                }

                .room-table th.text-center,
                .room-table td.text-center {
                    text-align: center;
                }

                .room-actions {
                    display: flex;
                    justify-content: center;
                }

                .room-actions.dropdown {
                    position: relative;
                }

                /* Nút răng cưa gọn, nằm giữa cột */
                .room-actions .action-gear {
                    min-width: 40px;
                    padding: .45rem .7rem;
                    border-radius: 999px;
                }

                /* MENU: bay ngang sang trái, canh giữa ô, không tràn xuống dòng dưới */
                .room-actions .dropdown-menu {
                    position: absolute;
                    top: 50% !important;
                    /* lấy mốc giữa ô Thao tác */
                    right: 110%;
                    /* bật ngang sang trái của nút răng cưa */
                    left: auto;
                    transform: translateY(-50%);
                    /* canh giữa theo chiều dọc */
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

                /* Item trong dropdown: icon + chữ đẹp, hover nhẹ */
                .room-actions .dropdown-item {
                    display: flex;
                    align-items: center;
                    gap: .55rem;
                    padding: .42rem .9rem;
                    color: #4b5563;
                }

                .room-actions .dropdown-item i {
                    width: 16px;
                    text-align: center;
                }

                .room-actions .dropdown-item:hover {
                    background: #eef2ff;
                    color: #111827;
                }

                /* Riêng nút Xóa giữ màu đỏ */
                .room-actions .dropdown-item.text-danger,
                .room-actions .dropdown-item.text-danger:hover {
                    color: #dc2626;
                    font-weight: 500;
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
                    transition: transform .2s ease, box-shadow .2s ease;
                }

                .btn-dergin:hover {
                    transform: translateY(-1px);
                    box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                    color: #fff;
                }

                .btn-dergin i {
                    font-size: .8rem;
                }

                .btn-dergin--muted {
                    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
                }

                .btn-dergin--info {
                    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
                }

                .btn-dergin--danger {
                    background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%);
                }

                .avatar-56 {
                    width: 56px;
                    height: 56px;
                    border-radius: 12px;
                    /* bo góc, không tròn nữa */
                    object-fit: cover;
                    border: 2px solid #e5e7eb;
                    /* viền nhạt */
                    background: #fff;
                }



                @media (max-width: 992px) {
                    .room-table thead {
                        display: none;
                    }

                    .room-table tbody {
                        display: block;
                    }

                    .room-table tbody tr {
                        display: flex;
                        flex-direction: column;
                        padding: 1rem;
                    }

                    .room-table tbody td {
                        display: flex;
                        justify-content: space-between;
                        padding: .35rem 0;
                    }
                }
                
            </style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('hoanThanhModal');
        const form = document.getElementById('hoanThanhForm');
        const paymentGroup = document.getElementById('payment_amount_group');
        const paymentToggle = document.getElementById('co_thanh_toan');
        const paymentInput = document.getElementById('payment_amount');
        const percentRange = document.getElementById('completion_percent');
        const percentValue = document.getElementById('completion_percent_value');
        if (percentRange && percentValue) {
            const sync = () => percentValue.textContent = (percentRange.value || 0) + '%';
            percentRange.addEventListener('input', sync);
            sync();
        }

        const updatePaymentVisibility = () => {
            const show = paymentToggle && paymentToggle.checked;
            if (show) {
                paymentGroup.classList.remove('d-none');
                paymentInput && paymentInput.setAttribute('required', 'required');
            } else {
                paymentGroup.classList.add('d-none');
                if (paymentInput) {
                    paymentInput.removeAttribute('required');
                    paymentInput.value = '';
                }
            }
        };
        if (paymentToggle) {
            paymentToggle.addEventListener('change', updatePaymentVisibility);
        }
        updatePaymentVisibility();

        const closeAllMenus = () => {
            document.querySelectorAll('.room-actions .dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
        };

        const showHoanThanhModal = (trigger) => {
            if (!trigger) return;
            const id = trigger.getAttribute('data-id');
            const ngay = trigger.getAttribute('data-ngay');
            document.getElementById('suco_id').value = id;
            document.getElementById('ngay_hoan_thanh').value = ngay || '';
            form.action = "{{ route('suco.hoanthanh', ':id') }}".replace(':id', id);
            if (paymentToggle) paymentToggle.checked = false;
            updatePaymentVisibility();
            try {
                const bsModal = bootstrap.Modal.getOrCreateInstance(modal);
                bsModal.show();
            } catch (_) {
                modal.classList.add('show');
                modal.style.display = 'block';
                modal.removeAttribute('aria-hidden');
                modal.setAttribute('aria-modal', 'true');
                if (!document.querySelector('.modal-backdrop')) {
                    const backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
                document.body.classList.add('modal-open');
            }
        };

        document.addEventListener('click', function(e) {
            const gearBtn = e.target.closest('.action-gear');
            if (gearBtn) {
                e.preventDefault();
                const menu = gearBtn.closest('.room-actions')?.querySelector('.dropdown-menu');
                const isOpen = menu && menu.classList.contains('show');
                closeAllMenus();
                if (menu && !isOpen) {
                    menu.classList.add('show');
                }
                return;
            }

            const deleteBtn = e.target.closest('.btn-delete-su-co');
            if (deleteBtn) {
                e.preventDefault();
                const formId = deleteBtn.getAttribute('data-form-id');
                if (formId && confirm('Bạn có chắc muốn xóa sự cố này không?')) {
                    const targetForm = document.getElementById(formId);
                    if (targetForm) {
                        targetForm.submit();
                    }
                }
                closeAllMenus();
                return;
            }

            const modalTrigger = e.target.closest('[data-bs-target="#hoanThanhModal"][data-id]');
            if (modalTrigger) {
                closeAllMenus();
                showHoanThanhModal(modalTrigger);
                return;
            }

            if (e.target.closest('.room-actions .dropdown-item')) {
                closeAllMenus();
            } else if (!e.target.closest('.room-actions')) {
                closeAllMenus();
            }
        });

        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const ngay = button.getAttribute('data-ngay');
            document.getElementById('suco_id').value = id;
            document.getElementById('ngay_hoan_thanh').value = ngay || '';
            form.action = "{{ route('suco.hoanthanh', ':id') }}".replace(':id', id);
            if (paymentToggle) paymentToggle.checked = false;
            updatePaymentVisibility();
        });
        // Fallback đóng modal khi không có Bootstrap
        document.addEventListener('click', function(e) {
            const closeBtn = e.target.closest('[data-bs-dismiss="modal"]');
            if (!closeBtn) return;
            const parentModal = closeBtn.closest('.modal');
            if (!parentModal) return;
            try {
                const bsModal = bootstrap.Modal.getOrCreateInstance(parentModal);
                bsModal.hide();
            } catch (_) {
                parentModal.classList.remove('show');
                parentModal.style.display = 'none';
                parentModal.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            }
        });
    });
</script>
@endpush
@endsection