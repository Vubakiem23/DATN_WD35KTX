@extends('public.layouts.app')

@section('title', 'Tin tức')

@section('content')
<section class="news-listing-section py-5">
    <div class="container">
        @if($tintucs->count() > 0)
            <div class="row g-4">
                @foreach($tintucs as $tin)
                    <div class="col-lg-4 col-md-6">
                        <article class="news-card">
                            <a href="{{ route('public.tintuc.show', $tin->slug) }}" class="news-thumb">
                                <img src="{{ $tin->hinh_anh ? asset($tin->hinh_anh) : asset('images/logo.png') }}" alt="{{ $tin->tieu_de }}">
                                <span class="news-label">Tin tức</span>
                            </a>
                            <div class="news-content">
                                <div class="news-date">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($tin->ngay_tao)->format('d/m/Y') }}
                                </div>
                                <h3 class="news-title">
                                    <a href="{{ route('public.tintuc.show', $tin->slug) }}">{{ $tin->tieu_de }}</a>
                                </h3>
                                <p class="news-excerpt">{{ Str::limit(strip_tags($tin->noi_dung), 140) }}</p>
                                <a href="{{ route('public.tintuc.show', $tin->slug) }}" class="news-link">
                                    Đọc tiếp <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $tintucs->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state text-center">
                <div class="empty-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3>Chưa có tin tức nào</h3>
                <p>Hãy quay lại vào thời gian tới để cập nhật thêm hoạt động và sự kiện của ký túc xá.</p>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .news-listing-section {
        background: #f9fafc;
    }

    .news-card {
        background: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        border: 1px solid rgba(15, 23, 42, 0.05);
    }

    .news-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.12);
    }

    .news-thumb {
        position: relative;
        display: block;
        height: 220px;
        overflow: hidden;
    }

    .news-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .news-card:hover .news-thumb img {
        transform: scale(1.05);
    }

    .news-label {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(255, 255, 255, 0.9);
        color: #1d4ed8;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .news-content {
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1;
    }

    .news-date {
        font-size: 14px;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .news-date i {
        color: #1d4ed8;
    }

    .news-title {
        font-size: 20px;
        font-weight: 700;
        line-height: 1.4;
        margin: 0;
    }

    .news-title a {
        color: #0f172a;
        text-decoration: none;
    }

    .news-title a:hover {
        color: #1d4ed8;
    }

    .news-excerpt {
        color: #475569;
        margin: 0;
        line-height: 1.6;
        flex: 1;
    }

    .news-link {
        font-weight: 600;
        color: #1d4ed8;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .news-link i {
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-link i {
        transform: translateX(4px);
    }

    .empty-state {
        background: #fff;
        padding: 60px 40px;
        border-radius: 24px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    }

    .empty-icon {
        width: 90px;
        height: 90px;
        border-radius: 24px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(29, 78, 216, 0.08);
        color: #1d4ed8;
        font-size: 36px;
    }

    @media (max-width: 575px) {
        .news-content {
            padding: 18px;
        }
    }
</style>
@endpush
@endsection
