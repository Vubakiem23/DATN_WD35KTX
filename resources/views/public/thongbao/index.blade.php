@extends('public.layouts.app')

@section('title', 'Thông báo | Ký túc xá VaMos')

@section('content')


<section class="notice-listing-section py-5">
    <div class="container">
        @if($thongbaos->count() > 0)
            <div class="notice-list">
                @foreach($thongbaos as $tb)
                    <a href="{{ route('public.thongbao.show', $tb->id) }}" class="notice-card">
                        <div class="notice-thumb">
                            <img src="{{ $tb->anh ? Storage::url($tb->anh) : asset('images/logo.png') }}" alt="{{ $tb->tieuDe->ten_tieu_de ?? 'Thông báo' }}">
                        </div>
                        <div class="notice-body">
                            <div class="notice-meta">
                                <span class="notice-category">Thông báo</span>
                                <span class="notice-date">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}
                                </span>
                            </div>
                            <h3 class="notice-title">{{ $tb->tieuDe->ten_tieu_de ?? 'Không có tiêu đề' }}</h3>
                            <p class="notice-excerpt">{{ Str::limit(strip_tags($tb->noi_dung), 150) }}</p>

                            <div class="notice-tags">
                                @if(!empty($tb->doi_tuong))
                                    <span class="notice-tag">Đối tượng: {{ $tb->doi_tuong }}</span>
                                @endif
                                @if(!empty($tb->muc_do))
                                    <span class="notice-tag">Mức độ: {{ $tb->muc_do }}</span>
                                @endif
                                @if($tb->phongs->count() > 0)
                                    <span class="notice-tag">
                                        Phòng: {{ $tb->phongs->map(fn($phong) => $phong->ten_phong ?? $phong->ma_phong)->join(', ') }}
                                    </span>
                                @endif
                                @if($tb->khus->count() > 0)
                                    <span class="notice-tag">
                                        Khu: {{ $tb->khus->pluck('ten_khu')->join(', ') }}
                                    </span>
                                @endif
                            </div>

                            <div class="notice-footer">
                                <span class="notice-link">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $thongbaos->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="empty-state text-center">
                <div class="empty-icon">
                    <i class="fas fa-bell-slash"></i>
                </div>
                <h3>Chưa có thông báo nào</h3>
                <p>Vui lòng quay lại sau để cập nhật các thông báo mới nhất từ ký túc xá.</p>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
    .page-hero {
        background: linear-gradient(135deg, #1a237e, #3949ab);
        color: #fff;
        padding: 60px 0;
        text-align: left;
        position: relative;
        overflow: hidden;
    }

    .page-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.25), transparent 60%);
        opacity: 0.6;
    }

    .page-hero .container {
        position: relative;
        z-index: 1;
        max-width: 900px;
    }

    .hero-kicker {
        text-transform: uppercase;
        letter-spacing: 2px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.9);
    }

    .page-hero h1 {
        font-size: 42px;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .hero-subtitle {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 0;
        line-height: 1.6;
    }

    .notice-listing-section {
        background: #f5f6fb;
    }

    .notice-list {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .notice-card {
        display: flex;
        gap: 24px;
        background: #fff;
        border-radius: 20px;
        padding: 24px;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(15, 23, 42, 0.06);
        align-items: stretch;
    }

    .notice-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .notice-thumb {
        width: 220px;
        border-radius: 16px;
        overflow: hidden;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    .notice-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .notice-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .notice-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
        color: #6b7280;
        flex-wrap: wrap;
        gap: 8px;
    }

    .notice-category {
        font-weight: 700;
        color: #ef4444;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 13px;
    }

    .notice-date i {
        margin-right: 6px;
        color: #1d4ed8;
    }

    .notice-title {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .notice-excerpt {
        color: #4b5563;
        margin-bottom: 0;
        line-height: 1.6;
    }

    .notice-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .notice-tag {
        background: #eef2ff;
        color: #4338ca;
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
    }

    .notice-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        font-size: 14px;
        color: #6b7280;
        flex-wrap: wrap;
        gap: 10px;
    }

    .notice-tags i {
        color: #ef4444;
        margin-right: 6px;
    }

    .notice-link {
        font-weight: 600;
        color: #1d4ed8;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .notice-link i {
        transition: transform 0.3s ease;
    }

    .notice-card:hover .notice-link i {
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

    @media (max-width: 991px) {
        .notice-card {
            flex-direction: column;
        }

        .notice-thumb {
            width: 100%;
            height: 220px;
        }
    }

    @media (max-width: 575px) {
        .page-hero {
            padding: 40px 0;
        }

        .page-hero h1 {
            font-size: 32px;
        }

        .notice-card {
            padding: 18px;
        }

    }
</style>
@endpush
@endsection
