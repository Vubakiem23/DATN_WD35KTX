@extends('admin.layouts.admin')
@section('title', 'Chi tiết phản hồi sinh viên')
@section('content')
    @php
        $sinhVien = \App\Models\User::where('id', $phanHoi->sinh_vien_id)->first();
    @endphp
    <div class="container-fluid">
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h4 class="mb-0">
                        <i class="fa fa-comments me-2"></i>Chi tiết phản hồi #{{ $phanHoi->id }}
                    </h4>
                    <a href="{{ route('admin.phan_hoi.list') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- Thông tin sinh viên -->
                <div class="row mb-3">
                    <div class="col-sm-6 mb-2">
                        <label class="text-muted small d-block">Sinh viên</label>
                        <span class="fw-semibold">{{ $sinhVien->name ?? 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="text-muted small d-block">Email</label>
                        <span>{{ $sinhVien->email ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6 mb-2">
                        <label class="text-muted small d-block">Ngày gửi</label>
                        <span>{{ $phanHoi->created_at ? $phanHoi->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6 mb-2">
                        <label class="text-muted small d-block">Trạng thái</label>
                        @if($phanHoi->trang_thai == 1)
                            <span class="badge bg-success">Đã xử lý</span>
                        @elseif($phanHoi->trang_thai == 2)
                            <span class="badge bg-danger">Đã từ chối</span>
                        @else
                            <span class="badge bg-warning text-dark">Chờ xử lý</span>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Nội dung phản hồi -->
                <div class="mb-3">
                    <label class="text-muted small d-block">Tiêu đề</label>
                    <p class="fw-semibold mb-0">{{ $phanHoi->tieu_de }}</p>
                </div>

                <div class="mb-3">
                    <label class="text-muted small d-block">Nội dung</label>
                    <div class="p-3 bg-light rounded" style="word-break: break-word;">
                        {!! nl2br(e($phanHoi->noi_dung)) !!}
                    </div>
                </div>

                <hr>

                <!-- Form đổi trạng thái -->
                <form method="POST" action="{{ route('admin.phan_hoi.update', $phanHoi) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="tieu_de" value="{{ $phanHoi->tieu_de }}">
                    <input type="hidden" name="noi_dung" value="{{ $phanHoi->noi_dung }}">
                    
                    <div class="row align-items-end">
                        <div class="col-sm-6 col-md-4 mb-2">
                            <label for="trang_thai" class="form-label small">Cập nhật trạng thái</label>
                            <select name="trang_thai" id="trang_thai" class="form-control form-control-sm">
                                <option {{ $phanHoi->trang_thai == 0 ? 'selected' : '' }} value="0">Chờ xử lý</option>
                                <option {{ $phanHoi->trang_thai == 1 ? 'selected' : '' }} value="1">Đã xử lý</option>
                                <option {{ $phanHoi->trang_thai == 2 ? 'selected' : '' }} value="2">Từ chối</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-4 mb-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fa fa-save me-1"></i> Lưu
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
