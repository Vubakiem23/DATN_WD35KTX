    @extends('admin.layouts.admin')

@section('title', 'Chỉnh sửa báo cáo sự cố')

    @section('content')
    <div class="container mt-4">

        @push('styles')
        <style>
            .page-title {
                font-size: 1.5rem;
                font-weight: 700;
                color: #1f2937;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .page-title i {
                color: #f59e0b;
            }

            .section-title {
                font-weight: 600;
                margin-bottom: 16px;
                display: flex;
                align-items: center;
                gap: 8px;
                color: #1f2937;
                font-size: 1rem;
            }

            .section-title .dot {
                width: 8px;
                height: 8px;
                background: #4e54c8;
                border-radius: 50%;
                display: inline-block;
            }

            .form-section {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 14px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
                margin-bottom: 20px;
                transition: box-shadow 0.2s ease;
            }

            .form-section:hover {
                box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            }

            .form-section .card-body {
                padding: 24px;
            }

            .form-label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .form-control,
            .form-select {
                border-radius: 10px;
                border: 1px solid #e5e7eb;
                padding: 0.65rem 1rem;
                transition: all 0.2s ease;
                font-size: 0.9rem;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #4e54c8;
                box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
                outline: none;
            }

            .form-control.is-invalid,
            .form-select.is-invalid {
                border-color: #ef4444;
            }

            .form-control.is-invalid:focus,
            .form-select.is-invalid:focus {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
            }

            .form-control:read-only,
            .form-control:disabled {
                background-color: #f9fafb;
                cursor: not-allowed;
            }

            .form-check-input {
                width: 1.25rem;
                height: 1.25rem;
                margin-top: 0.25rem;
                border-radius: 6px;
                border: 2px solid #d1d5db;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .form-check-input:checked {
                background-color: #4e54c8;
                border-color: #4e54c8;
            }

            .form-check-input:focus {
                box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
            }

            .form-check-label {
                margin-left: 0.5rem;
                font-weight: 500;
                color: #495057;
                cursor: pointer;
            }

            .btn-dergin {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: .35rem;
                padding: .5rem 1.2rem;
                border-radius: 999px;
                font-weight: 600;
                font-size: .85rem;
                border: none;
                color: #fff;
                background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
                box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
                transition: transform .2s ease, box-shadow .2s ease;
                text-decoration: none;
            }

            .btn-dergin:hover {
                transform: translateY(-1px);
                box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
                color: #fff;
            }

            .btn-dergin i {
                font-size: .8rem;
            }

            .btn-dergin--warning {
                background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
                box-shadow: 0 6px 16px rgba(245, 158, 11, .22);
            }

            .btn-dergin--warning:hover {
                box-shadow: 0 10px 22px rgba(245, 158, 11, .32);
            }

            .btn-dergin--muted {
                background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
                box-shadow: 0 6px 16px rgba(107, 114, 128, .22);
            }

            .btn-dergin--muted:hover {
                box-shadow: 0 10px 22px rgba(107, 114, 128, .32);
            }

            .invalid-feedback {
                display: block;
                width: 100%;
                margin-top: 0.25rem;
                font-size: 0.875rem;
                color: #ef4444;
            }

            .text-danger {
                color: #ef4444 !important;
            }

            .image-preview {
                width: 120px;
                height: 80px;
                object-fit: cover;
                border-radius: 8px;
                border: 2px solid #e5e7eb;
                margin-bottom: 0.5rem;
            }
        </style>
        @endpush

        <h4 class="page-title mb-0">
            <i class="fa fa-edit"></i>
            Chỉnh sửa sự cố #{{ $suco->id }}
        </h4>
        <p class="text-muted mb-4">Cập nhật thông tin và trạng thái xử lý sự cố.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
        @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fa fa-exclamation-circle"></i> Vui lòng kiểm tra lại thông tin đã nhập.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
            @endif

            <form action="{{ route('suco.update', $suco->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

            {{-- Thông tin sinh viên và phòng --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin sinh viên</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                    <label class="form-label">Sinh viên</label>
                    <input type="text" class="form-control" 
                                value="{{ $suco->sinhVien->ho_ten }} ({{ $suco->sinhVien->ma_sinh_vien }})" 
                                disabled>
                </div>

                        <div class="col-md-6">
                    <label class="form-label">Phòng / Khu</label>
                    @php
                        // Ưu tiên lấy phòng từ slot (nếu có), nếu không thì lấy từ phong_id trực tiếp
                        $student = $suco->sinhVien ?? null;
                        $phong = null;
                        if ($student) {
                            // Kiểm tra slot và phong của slot
                            if (isset($student->slot) && $student->slot && isset($student->slot->phong) && $student->slot->phong) {
                                $phong = $student->slot->phong;
                            } elseif (isset($student->phong) && $student->phong) {
                                $phong = $student->phong;
                            } elseif (isset($suco->phong) && $suco->phong) {
                                $phong = $suco->phong;
                            }
                        } elseif (isset($suco->phong) && $suco->phong) {
                            $phong = $suco->phong;
                        }
                        $tenPhongDisplay = $phong && isset($phong->ten_phong) ? $phong->ten_phong : 'Chưa có phòng';
                        $khu = ($phong && isset($phong->khu) && $phong->khu) ? $phong->khu : null;
                        $tenKhuDisplay = $khu && isset($khu->ten_khu) ? $khu->ten_khu : null;
                    @endphp
                    <input type="text" class="form-control" 
                                value="{{ $tenPhongDisplay }}@if($tenKhuDisplay) - Khu {{ $tenKhuDisplay }}@endif" 
                                disabled>
                        </div>
                    </div>
                </div>
                </div>

            {{-- Chi tiết sự cố --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Chi tiết sự cố</div>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="mo_ta" class="form-label">Mô tả sự cố <span class="text-danger">*</span></label>
                            <textarea name="mo_ta" id="mo_ta" rows="5" 
                                class="form-control @error('mo_ta') is-invalid @enderror" 
                                placeholder="Nhập mô tả chi tiết về sự cố..." required>{{ old('mo_ta', $suco->mo_ta) }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>

                        <div class="col-md-6">
                            <label for="trang_thai" class="form-label">Trạng thái xử lý <span class="text-danger">*</span></label>
                            <select name="trang_thai" id="trang_thai" 
                                class="form-select @error('trang_thai') is-invalid @enderror" required>
                                <option value="Tiếp nhận" {{ old('trang_thai', $suco->trang_thai) == 'Tiếp nhận' ? 'selected' : '' }}>Tiếp nhận</option>
                                <option value="Đang xử lý" {{ old('trang_thai', $suco->trang_thai) == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="Hoàn thành" {{ old('trang_thai', $suco->trang_thai) == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                    </select>
                            @error('trang_thai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>

                        <div class="col-md-6" id="hoan_thanh_field" 
                            style="display: {{ old('trang_thai', $suco->trang_thai) == 'Hoàn thành' ? 'block' : 'none' }}">
                    <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành</label>
                            <input type="date" name="ngay_hoan_thanh" id="ngay_hoan_thanh" 
                                class="form-control @error('ngay_hoan_thanh') is-invalid @enderror"
                        value="{{ old('ngay_hoan_thanh', $suco->ngay_hoan_thanh ? \Carbon\Carbon::parse($suco->ngay_hoan_thanh)->format('Y-m-d') : '') }}">
                            @error('ngay_hoan_thanh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="anh" class="form-label">Ảnh minh chứng</label>
                            @if($suco->anh)
                                <div class="mb-2">
                                    <img src="{{ asset($suco->anh) }}" alt="Ảnh sự cố" class="image-preview">
                                </div>
                            @endif
                            <input type="file" name="anh" id="anh" 
                                class="form-control @error('anh') is-invalid @enderror" 
                                accept="image/*">
                            @error('anh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                </div>

            <!-- {{-- Thông tin thanh toán --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin thanh toán</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                    <label for="payment_amount" class="form-label">Giá tiền (₫)</label>
                            <input type="number" name="payment_amount" id="payment_amount" 
                                class="form-control @error('payment_amount') is-invalid @enderror" 
                                step="0.01" min="0"
                        value="{{ old('payment_amount', $suco->payment_amount) }}">
                            @error('payment_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                </div>

                        <div class="col-md-6">
                            <label class="form-label d-block">&nbsp;</label>
                            <div class="form-check mt-2">
                    <input type="checkbox" name="is_paid" class="form-check-input" id="is_paid"
                                    value="1" {{ old('is_paid', $suco->is_paid) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_paid">Đã thanh toán</label>
                </div>
                        </div>
                    </div>
                </div>
                </div> -->

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--warning">
                    <i class="fa fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('suco.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
            </form>
    </div>

    {{-- Script: Tự động ẩn/hiện ngày hoàn thành --}}
    <script>
    document.getElementById('trang_thai').addEventListener('change', function() {
        const field = document.getElementById('hoan_thanh_field');
        field.style.display = (this.value === 'Hoàn thành') ? 'block' : 'none';
    });
    </script>
    @endsection
