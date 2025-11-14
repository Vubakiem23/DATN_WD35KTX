@extends('admin.layouts.admin')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container py-3">

    {{-- I. Ảnh thông báo --}}
    @if (!empty($thongbao->anh))
    <div class="text-center mb-4">
        <img src="{{ asset('storage/' . $thongbao->anh) }}"
            alt="{{ $thongbao->tieuDe->ten_tieu_de ?? 'Ảnh thông báo' }}"
            class="img-fluid shadow border rounded-3 thongbao-img">
    </div>
    @endif

    {{-- II. Thông tin chi tiết --}}
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-info-circle-fill me-2"></i> Thông tin chi tiết thông báo
        </div>
        <div class="card-body p-3">

            <div class="row mb-2">
                <div class="col-md-6"><strong>Tiêu đề:</strong> {{ $thongbao->tieuDe->ten_tieu_de ?? '-' }}</div>
                <div class="col-md-6">
                    <strong>Ngày đăng:</strong>
                    {{ $thongbao->ngay_dang ? \Carbon\Carbon::parse($thongbao->ngay_dang)->format('d/m/Y') : '-' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6"><strong>Đối tượng:</strong> {{ $thongbao->doi_tuong ?? '-' }}</div>
                <div class="col-md-6">
                    <strong>Mức độ:</strong>
                    @php
                    $mucDo = $thongbao->mucDo->ten_muc_do ?? 'Khác';
                    $badgeColor = match(strtolower($mucDo)) {
                    'cao' => 'bg-danger text-white',
                    'trung bình' => 'bg-warning text-dark',
                    'thấp' => 'bg-success text-white',
                    default => 'bg-secondary text-white',
                    };
                    @endphp
                    <span class="badge {{ $badgeColor }}">{{ $mucDo }}</span>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Phòng:</strong>
                    @if($thongbao->phongs->count())
                    @foreach($thongbao->phongs as $phong)
                    <span class="badge bg-light text-dark border me-1">{{ $phong->ten_phong }}</span>
                    @endforeach
                    @else
                    -
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>Khu:</strong>
                    @if($thongbao->khus->count())
                    @foreach($thongbao->khus as $khu)
                    <span class="badge bg-light text-dark border me-1">{{ $khu->ten_khu }}</span>
                    @endforeach
                    @else
                    -
                    @endif
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>File đính kèm:</strong>
                    @if($thongbao->file)
                    <a href="{{ asset('storage/' . $thongbao->file) }}" download class="btn btn-sm btn-outline-primary me-2">
                        <i class="bi bi-download"></i> Tải xuống
                    </a>
                    <span class="badge bg-light text-dark border">
                        {{ pathinfo($thongbao->file, PATHINFO_BASENAME) }}
                    </span>
                    @else
                    -
                    @endif
                </div>
                <div class="col-md-6">
                    <strong>Người đăng:</strong> {{ $thongbao->nguoiDang->fullname ?? '—' }}
                </div>
            </div>

        </div>
    </div>

    {{-- III. Nội dung thông báo --}}
    <div class="card border-success mb-3">
        <div class="card-header bg-success text-white fw-bold">
            <i class="bi bi-chat-dots-fill me-2"></i> Nội dung thông báo
        </div>
        <div class="card-body p-3">
            {!! $thongbao->noi_dung ?? '<p class="text-muted">Không có nội dung</p>' !!}
        </div>
    </div>

    {{-- Nút quay lại --}}
    <div class="text-center mt-3">
        <a href="{{ route('thongbao.index') }}" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

{{-- Style đồng bộ --}}
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        font-size: 1rem;
        letter-spacing: 0.2px;
    }

    img.rounded {
        border: 3px solid #dee2e6;
    }

    .badge {
        font-size: 0.9rem;
    }

    table {
        background: white;
        border-radius: 10px;
    }

    .thongbao-img {
        max-width: 300px;
        height: auto;
        object-fit: cover;
        border: 3px solid #dee2e6;
        transition: transform 0.2s ease-in-out;
    }

    .thongbao-img:hover {
        transform: scale(1.03);
    }
</style>
@endsection