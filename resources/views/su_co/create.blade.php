@extends('admin.layouts.admin')

@section('title', 'Báo cáo sự cố mới')

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
                color: #4e54c8;
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

            .form-control:read-only {
                background-color: #f9fafb;
                cursor: not-allowed;
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

            .btn-dergin--success {
                background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
                box-shadow: 0 6px 16px rgba(16, 185, 129, .22);
            }

            .btn-dergin--success:hover {
                box-shadow: 0 10px 22px rgba(16, 185, 129, .32);
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
        </style>
        @endpush

        <h4 class="page-title mb-0">
            <i class="fa fa-exclamation-triangle"></i>
            Báo cáo sự cố mới
        </h4>
        <p class="text-muted mb-4">Tạo báo cáo sự cố mới cho sinh viên.</p>

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

        <form action="{{ route('suco.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Thông tin sinh viên và phòng --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin sinh viên</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sinh_vien_id" class="form-label">Sinh viên <span class="text-danger">*</span></label>
                            <select name="sinh_vien_id" id="sinh_vien_id" 
                                class="form-select @error('sinh_vien_id') is-invalid @enderror" required>
                    <option value="">-- Chọn sinh viên --</option>
                    @foreach($sinhviens as $sv)
                        @php
                            // Ưu tiên lấy phòng từ slot
                            $phong = null;
                            if (isset($sv->slot) && $sv->slot && isset($sv->slot->phong) && $sv->slot->phong) {
                                $phong = $sv->slot->phong;
                            } elseif (isset($sv->phong) && $sv->phong) {
                                $phong = $sv->phong;
                            }
                            $tenPhong = $phong && isset($phong->ten_phong) ? $phong->ten_phong : 'Chưa có phòng';
                            $phongId = $phong && isset($phong->id) ? $phong->id : '';
                        @endphp
                        <option value="{{ $sv->id }}" 
                                data-phong="{{ $tenPhong }}"
                                            data-phong-id="{{ $phongId }}"
                                            @selected(old('sinh_vien_id') == $sv->id)>
                            {{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }})
                        </option>
                    @endforeach
                </select>
                            @error('sinh_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
            </div>

                        <div class="col-md-6">
                <label for="phong_ten" class="form-label">Phòng</label>
                            <input type="text" id="phong_ten" 
                                class="form-control @error('phong_id') is-invalid @enderror" 
                                name="phong_ten" 
                                value="Chưa chọn sinh viên" 
                                readonly>
                <input type="hidden" name="phong_id" id="phong_id">
                            @error('phong_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                placeholder="Nhập mô tả chi tiết về sự cố..." required>{{ old('mo_ta') }}</textarea>
                            @error('mo_ta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
            </div>

                        <div class="col-md-6">
                <label for="anh" class="form-label">Ảnh minh chứng (nếu có)</label>
                            <input type="file" name="anh" id="anh" 
                                class="form-control @error('anh') is-invalid @enderror" 
                                accept="image/*">
                            @error('anh')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
            </div>

                        <div class="col-md-6">
                            <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành sự cố</label>
                            <input type="text" id="ngay_hoan_thanh" 
                                class="form-control" 
                                value="--- Chưa hoàn thành ---" 
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--success">
                    <i class="fa fa-paper-plane"></i> Gửi báo cáo
                </button>
                <a href="{{ route('suco.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
</div>

    {{-- Script tự động lấy phòng --}}
<script>
document.getElementById('sinh_vien_id').addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let phongTen = selectedOption.getAttribute('data-phong') || 'Chưa có phòng';
    let phongId = selectedOption.getAttribute('data-phong-id') || '';
    
    document.getElementById('phong_ten').value = phongTen;
    document.getElementById('phong_id').value = phongId;
});
</script>
@endsection
