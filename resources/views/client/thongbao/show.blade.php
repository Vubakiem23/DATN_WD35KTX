@extends('client.layouts.app')

@section('title', ($thongbao->tieuDe->ten_tieu_de ?? 'Thông báo') . ' - Sinh viên')

@section('content')
<!-- Header màu xanh đậm -->
<div class="page-header-dark mb-4">
    <div class="d-flex justify-content-center align-items-center py-4 px-4">
        <h4 class="mb-0 text-white fw-bold">
            <i class="fas fa-bell me-2"></i>
            Chi tiết thông báo
        </h4>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('client.dashboard') }}">
                                <i class="fas fa-home"></i> Trang chủ
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('client.thongbao.index') }}">Thông báo</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ Str::limit($thongbao->tieuDe->ten_tieu_de ?? 'Chi tiết', 30) }}
                        </li>
                    </ol>
                </nav>

                <!-- Tiêu đề -->
                <h2 class="card-title mb-3 fw-bold text-primary">
                    {{ $thongbao->tieuDe->ten_tieu_de ?? 'Không có tiêu đề' }}
                </h2>

                <!-- Meta thông tin -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-4 text-muted">
                    <small>
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ \Carbon\Carbon::parse($thongbao->ngay_dang)->format('d/m/Y H:i') }}
                    </small>
                    @if($thongbao->mucDo)
                    <span class="badge bg-warning">
                        {{ $thongbao->mucDo->ten_muc_do }}
                    </span>
                    @endif
                    @if($thongbao->doi_tuong)
                    <span class="badge bg-secondary">
                        {{ $thongbao->doi_tuong }}
                    </span>
                    @endif
                </div>

                <!-- Tags -->
                @if($thongbao->khus->count() > 0 || $thongbao->phongs->count() > 0)
                <div class="mb-4">
                    @if($thongbao->khus->count() > 0)
                    <span class="badge bg-info me-2 mb-2">
                        <i class="fas fa-building me-1"></i>
                        Khu: {{ $thongbao->khus->pluck('ten_khu')->join(', ') }}
                    </span>
                    @endif
                    @if($thongbao->phongs->count() > 0)
                    <span class="badge bg-success me-2 mb-2">
                        <i class="fas fa-door-open me-1"></i>
                        Phòng: {{ $thongbao->phongs->map(fn($phong) => $phong->ten_phong)->join(', ') }}
                    </span>
                    @endif
                </div>
                @endif

                <!-- Ảnh -->
                @if($thongbao->anh)
                <div class="mb-4 text-center">
                    <img src="{{ Storage::url($thongbao->anh) }}" 
                         alt="{{ $thongbao->tieuDe->ten_tieu_de ?? 'Thông báo' }}" 
                         class="img-fluid rounded" 
                         style="max-height: 400px; width: auto;">
                </div>
                @endif

                <!-- Nội dung -->
                <div class="news-content mb-4">
                    {!! $thongbao->noi_dung !!}
                </div>

                <!-- File đính kèm -->
                @if($thongbao->file)
                <div class="mb-4">
                    <a href="{{ Storage::url($thongbao->file) }}" 
                       target="_blank" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-file-download me-2"></i>
                        Tải file đính kèm
                    </a>
                </div>
                @endif

                <!-- Nút quay lại -->
                <div class="mt-4">
                    <a href="{{ route('client.thongbao.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar: Thông báo mới -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-newspaper me-2"></i>
                    Thông báo mới
                </h5>
            </div>
            <div class="card-body">
                @forelse($thongBaoMoi as $notify)
                <a href="{{ route('client.thongbao.show', $notify->id) }}" 
                   class="d-flex gap-3 mb-3 text-decoration-none border-bottom pb-3">
                    @if($notify->anh)
                    <div class="flex-shrink-0">
                        <img src="{{ Storage::url($notify->anh) }}" 
                             alt="{{ $notify->tieuDe->ten_tieu_de ?? 'Thông báo' }}" 
                             class="rounded" 
                             style="width: 80px; height: 80px; object-fit: cover;">
                    </div>
                    @endif
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-dark fw-bold">
                            {{ Str::limit($notify->tieuDe->ten_tieu_de ?? 'Thông báo', 50) }}
                        </h6>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i>
                            {{ \Carbon\Carbon::parse($notify->ngay_dang)->format('d/m/Y') }}
                        </small>
                    </div>
                </a>
                @empty
                <p class="text-muted mb-0">Chưa có thông báo nào khác.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header-dark {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .news-content {
        line-height: 1.8;
        color: #333;
    }

    .news-content img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 15px 0;
    }

    .news-content p {
        margin-bottom: 1rem;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
        margin-bottom: 0;
    }

    .breadcrumb-item a {
        color: #667eea;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        text-decoration: underline;
    }
</style>
@endpush
@endsection

