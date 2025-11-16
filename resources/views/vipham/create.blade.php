@extends('admin.layouts.admin')

@section('title', 'Ghi vi phạm')

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

            .input-group-text {
                border-radius: 0 10px 10px 0;
                border: 1px solid #e5e7eb;
                background-color: #f9fafb;
                color: #6b7280;
                font-weight: 600;
            }

            .input-group .form-control {
                border-radius: 10px 0 0 10px;
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
            Ghi vi phạm
        </h4>
        <p class="text-muted mb-4">Tạo biên bản vi phạm cho sinh viên.</p>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="POST" action="{{ route('vipham.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Thông tin vi phạm --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Thông tin vi phạm</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sinh_vien_id" class="form-label">Sinh viên <span class="text-danger">*</span></label>
                            <select name="sinh_vien_id" id="sinh_vien_id" 
                                class="form-select @error('sinh_vien_id') is-invalid @enderror" required>
                                <option value="">-- Chọn sinh viên --</option>
                                @foreach ($students as $st)
                                    <option value="{{ $st->id }}" @selected(($defaultStudentId ?? old('sinh_vien_id')) == $st->id)>
                                        {{ $st->ho_ten }} ({{ $st->ma_sinh_vien }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sinh_vien_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="violation_type_id" class="form-label">Loại vi phạm <span class="text-danger">*</span></label>
                            <select name="violation_type_id" id="violation_type_id" 
                                class="form-select @error('violation_type_id') is-invalid @enderror" required>
                                <option value="">-- Chọn loại --</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}" @selected(old('violation_type_id') == $t->id)>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('violation_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label for="image" class="form-label">Hình ảnh (nếu có)</label>
                            <input type="file" name="image" id="image" 
                                class="form-control @error('image') is-invalid @enderror" 
                                accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chi tiết vi phạm --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Chi tiết vi phạm</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="occurred_at" class="form-label">Ngày xảy ra <span class="text-danger">*</span></label>
                            <input type="date" name="occurred_at" id="occurred_at" 
                                value="{{ old('occurred_at') }}" 
                                class="form-control @error('occurred_at') is-invalid @enderror" required>
                            @error('occurred_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" id="status" 
                                class="form-select @error('status') is-invalid @enderror" required>
                                <option value="open" @selected(old('status', 'open') == 'open')>Chưa xử lý</option>
                                <option value="resolved" @selected(old('status') == 'resolved')>Đã xử lý</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="penalty_amount" class="form-label">Tiền phạt (nếu có)</label>
                            <div class="input-group">
                                <input type="number" step="1000" name="penalty_amount" id="penalty_amount" 
                                    value="{{ old('penalty_amount') }}"
                                    class="form-control @error('penalty_amount') is-invalid @enderror" 
                                    placeholder="VD: 200000">
                                <div class="input-group-append">
                                    <span class="input-group-text">VND</span>
                                </div>
                            </div>
                            @error('penalty_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biên lai & Ghi chú --}}
            <div class="form-section">
                <div class="card-body">
                    <div class="section-title"><span class="dot"></span>Biên lai & Ghi chú</div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="receipt_no" class="form-label">Số biên lai</label>
                            <input type="text" name="receipt_no" id="receipt_no" 
                                value="{{ old('receipt_no') }}" 
                                class="form-control @error('receipt_no') is-invalid @enderror"
                                placeholder="Tự sinh khi lưu" readonly>
                            @error('receipt_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-8">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea name="note" id="note" rows="4" 
                                class="form-control @error('note') is-invalid @enderror" 
                                placeholder="Mô tả ngắn tình huống / biên bản...">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn-dergin btn-dergin--success">
                    <i class="fa fa-save"></i> Lưu
                </button>
                <a href="{{ route('vipham.index') }}" class="btn-dergin btn-dergin--muted">
                    <i class="fa fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
@endsection
