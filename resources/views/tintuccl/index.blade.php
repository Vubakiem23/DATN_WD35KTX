<!-- @extends('public.layouts.app')

@section('title', 'Tin tức | Ký túc xá VaMos')

@section('hero')
<div class="hero-section small-hero">
    <img src="{{ asset('images/news-banner.jpg') }}" class="hero-image" alt="">
    <div class="hero-content">
        <h1 class="hero-title">TIN TỨC & SỰ KIỆN</h1>
        <p class="hero-subtitle">Thông tin mới nhất tại Ký túc xá VaMos</p>
    </div>
</div>
@endsection

@section('content')
<div class="container py-4">

    <h2 class="section-title mb-4">📢 TẤT CẢ TIN TỨC</h2>

    <div class="row">

        @foreach ($tintucs as $tin)
        <div class="col-md-4 mb-4">
            <div class="news-card">
                <div class="news-card-img">
                    <img src="{{ asset($tin->hinh_anh) }}" alt="Ảnh tin tức">
                </div>

                <div class="news-card-body">
                    <h5 class="news-title">{{ $tin->tieu_de }}</h5>

                    <p class="news-excerpt">
                        {!! Str::limit(strip_tags($tin->noi_dung), 120) !!}
                    </p>

                    <div class="news-date">
                        <i class="far fa-calendar-alt"></i>
                        {{ \Carbon\Carbon::parse($tin->ngay_tao)->format('d/m/Y') }}
                    </div>

                    <a href="{{ route('tintuccl.show', $tin->slug) }}" class="view-more-link">
                        Xem chi tiết →
                    </a>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center">
        {{ $tintucs->links() }}
    </div>
</div>
@endsection
@push('styles')
<style>
   .news-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: 0.3s;
}
.news-card:hover { transform: translateY(-4px); }

.news-card-img img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.news-card-body { padding: 15px; }

.news-title { font-size: 18px; font-weight: 700; }

.news-excerpt { color: #555; font-size: 14px; }

.news-date { font-size: 13px; color: #777; margin-bottom: 8px; }

.view-more-link { font-weight: 600; color: #007bff; }

.small-hero {
    height: 260px;
    position: relative;
    overflow: hidden;
}
.small-hero .hero-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.small-hero .hero-content {
    position: absolute;
    left: 30px;
    bottom: 30px;
    color: white;
}

</style>
@endpush -->