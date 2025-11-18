@extends('public.layouts.app')

@section('title', $tinTuc->tieu_de . ' | Tin tức')

@section('content')
<div class="news-detail-page py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="news-article panel h-100">
                    <div class="article-breadcrumb mb-3">
                        <a href="{{ route('public.home') }}">Trang chủ</a>
                        <span>/</span>
                        <a href="{{ route('public.tintuc.index') }}">Tin tức</a>
                        <span>/</span>
                        <span>{{ $tinTuc->tieu_de }}</span>
                    </div>

                    <h1 class="article-title">{{ $tinTuc->tieu_de }}</h1>

                    <div class="article-meta mb-4">
                        <span><i class="far fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::parse($tinTuc->ngay_tao)->format('d/m/Y') }}</span>
                        <span class="mx-3">•</span>
                        <span><i class="fas fa-map-marker-alt me-2 text-danger"></i>Ký túc xá VaMos</span>
                    </div>

                    @if($tinTuc->hashtags->count() > 0)
                        <div class="article-tags mb-4">
                            @foreach($tinTuc->hashtags as $tag)
                                <span class="article-tag">#{{ $tag->ten }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="news-detail-content">
                        {!! $tinTuc->noi_dung !!}
                    </div>

                    <div class="article-footer mt-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('public.tintuc.index') }}" class="view-more-link">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                        <div class="share-label text-muted">
                            <i class="fas fa-share-alt me-2"></i>Chia sẻ bài viết
                        </div>
                    </div>
                </article>
            </div>

            <div class="col-lg-4">
                <aside class="news-sidebar">
                    <div class="sidebar-block panel">
                        <div class="sidebar-title text-danger">TIN MỚI</div>
                        @forelse($tinMoi as $baiViet)
                            <a href="{{ route('public.tintuc.show', $baiViet->slug) }}" class="sidebar-news-item">
                                <div class="sidebar-thumb">
                                    <img src="{{ $baiViet->hinh_anh ? asset($baiViet->hinh_anh) : asset('images/logo.png') }}" alt="{{ $baiViet->tieu_de }}">
                                </div>
                                <div class="sidebar-news-content">
                                    <p class="sidebar-news-title">{{ $baiViet->tieu_de }}</p>
                                    <span class="sidebar-news-date">
                                        {{ \Carbon\Carbon::parse($baiViet->ngay_tao)->format('d/m/Y') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <p class="mb-0 text-muted small">Chưa có tin tức nào khác.</p>
                        @endforelse
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .news-detail-page {
        background-color: #f3f4f6;
    }

    .news-article {
        border-radius: 18px;
        padding: 32px;
    }

    .article-breadcrumb {
        font-size: 14px;
        color: #6b7280;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .article-breadcrumb a {
        color: #1d4ed8;
        text-decoration: none;
        font-weight: 600;
    }

    .article-title {
        font-size: 32px;
        font-weight: 800;
        line-height: 1.3;
        color: #111827;
    }

    .article-meta {
        font-size: 15px;
        color: #4b5563;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .article-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .article-tag {
        background-color: #eef2ff;
        color: #4338ca;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
    }

    .news-sidebar .sidebar-block {
        border-radius: 18px;
    }

    .sidebar-title {
        font-size: 18px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .sidebar-news-item {
        display: flex;
        gap: 12px;
        text-decoration: none;
        padding: 14px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .sidebar-news-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .sidebar-thumb {
        width: 72px;
        height: 72px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        background-color: #f9fafb;
    }

    .sidebar-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sidebar-news-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .sidebar-news-date {
        font-size: 13px;
        color: #6b7280;
    }

    @media (max-width: 991px) {
        .news-article {
            padding: 24px;
        }

        .article-title {
            font-size: 26px;
        }
    }
</style>
@endpush
@endsection