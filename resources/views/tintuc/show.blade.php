@extends('admin.layouts.admin')

@section('title', 'Chi tiết tin tức')

@section('content')
<div class="container mt-4" style="max-width: 900px;">

    {{-- Ảnh minh họa --}}
    @if(!empty($tinTuc->hinh_anh))
    {{-- I. Ảnh minh họa --}}
    <div class="text-center mb-4">
        <img src="{{ $tinTuc->hinh_anh && file_exists(public_path($tinTuc->hinh_anh)) 
                      ? asset($tinTuc->hinh_anh) 
                      : 'https://dummyimage.com/300x200/eff3f9/9aa8b8&text=No+Image' }}"
             alt="{{ $tinTuc->tieu_de ?? 'Ảnh tin tức' }}"
             class="img-fluid shadow border rounded-3 tintuc-img">
    </div>
    @endif

    {{-- Thông tin chi tiết --}}
    <div class="card border-primary mb-3">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-newspaper me-2"></i> Thông tin tin tức
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <strong>Tiêu đề:</strong> {{ $tinTuc->tieu_de ?? '-' }}
                </div>
                <div class="col-md-6">
                    <strong>Ngày đăng:</strong>
                    {{ $tinTuc->ngay_tao ? \Carbon\Carbon::parse($tinTuc->ngay_tao)->format('d/m/Y') : '-' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <strong>Hashtags:</strong>
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

    {{-- Nội dung --}}
    <div class="card border-success mb-3">
        <div class="card-header bg-success text-white fw-bold">
            <i class="bi bi-chat-dots-fill me-2"></i> Nội dung
        </div>
        <div class="card-body">
            {!! $tinTuc->noi_dung ?? '<p class="text-muted">Không có nội dung</p>' !!}
        </div>
    </div>

    {{-- Nút quay lại --}}
    <div class="text-center mt-3">
        <a href="{{ route('tintuc.index') }}" class="btn btn-secondary px-4">
            <i class="bi bi-arrow-left-circle me-1"></i> Quay lại danh sách
        </a>
    </div>
</div>

{{-- Style đồng bộ với create/edit --}}
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
        border-radius: 8px;
        transition: transform 0.2s ease-in-out;
    }

    .tintuc-img:hover {
        transform: scale(1.03);
    }
</style>
@endsection
