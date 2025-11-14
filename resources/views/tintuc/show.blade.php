@extends('admin.layouts.admin')

@section('title', 'Chi tiết tin tức')

@section('content')
<div class="container py-3">

    {{-- I. Ảnh minh họa --}}
    @if (!empty($tinTuc->anh))
    <div class="text-center mb-4">
        <img src="{{ asset('storage/' . $tinTuc->anh) }}"
            alt="{{ $tinTuc->tieu_de ?? 'Ảnh tin tức' }}"
            class="img-fluid shadow border rounded-3 tintuc-img">
    </div>
    @endif

    {{-- II. Thông tin chi tiết --}}
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-newspaper me-2"></i> Thông tin chi tiết tin tức
        </div>
        <div class="card-body p-3">

            <div class="row mb-2">
                <div class="col-md-6"><strong>Tiêu đề:</strong> {{ $tinTuc->tieu_de ?? '-' }}</div>
                <div class="col-md-6">
                    <strong>Ngày đăng:</strong>
                    {{ $tinTuc->ngay_tao ? \Carbon\Carbon::parse($tinTuc->ngay_tao)->format('d/m/Y') : '-' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Hashtag:</strong>
                    @if($tinTuc->hashtags->count())
                        @foreach($tinTuc->hashtags as $tag)
                            <span class="badge bg-light text-dark border me-1">#{{ $tag->ten }}</span>
                        @endforeach
                    @else
                        -
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- III. Nội dung tin tức --}}
    <div class="card border-success mb-3">
        <div class="card-header bg-success text-white fw-bold">
            <i class="bi bi-chat-dots-fill me-2"></i> Nội dung tin tức
        </div>
        <div class="card-body p-3">
            {!! $tinTuc->noi_dung ?? '<p class="text-muted">Không có nội dung</p>' !!}
        </div>
    </div>

    {{-- IV. Nút quay lại --}}
    <div class="text-center mt-3">
        <a href="{{ route('tintuc.index') }}" class="btn btn-secondary px-4">
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

    .badge {
        font-size: 0.9rem;
    }

    .tintuc-img {
        max-width: 300px;
        height: auto;
        object-fit: cover;
        border: 3px solid #dee2e6;
        transition: transform 0.2s ease-in-out;
    }

    .tintuc-img:hover {
        transform: scale(1.03);
    }
</style>
@endsection
