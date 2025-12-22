@extends('public.layouts.app')
@section('title', 'Chi tiết phản hồi sinh viên')
@section('content')
    <div class="content-section about-page">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="title mb-0">
                    Chi tiết phản hồi: #{{ $phanHoi->id }}
                </h3>
                <a href="{{ route('client.phan_hoi.list') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Ngày gửi</label>
                            <p class="mb-0">{{ $phanHoi->created_at ? $phanHoi->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Trạng thái</label>
                            <p class="mb-0">
                                @if($phanHoi->trang_thai == 1)
                                    <span class="badge bg-success">Đã xử lý</span>
                                @elseif($phanHoi->trang_thai == 2)
                                    <span class="badge bg-danger">Đã từ chối</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="text-muted small">Tiêu đề</label>
                        <p class="fw-semibold fs-5">{{ $phanHoi->tieu_de }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted small">Nội dung</label>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($phanHoi->noi_dung)) !!}
                        </div>
                    </div>

                    @if($phanHoi->trang_thai == 0)
                        <hr>
                        <a href="{{ route('client.phan_hoi.edit', $phanHoi) }}" class="btn btn-primary">
                            <i class="fa fa-edit me-1"></i> Sửa phản hồi
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
