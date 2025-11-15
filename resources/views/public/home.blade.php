@extends('public.layouts.app')

@section('title', 'Trang chủ | KÝ TÚC XÁ')

@section('hero')
@php
    $heroSlides = [
        [
            'src' => asset('images/lovepik-different-male-college-students-in-the-dormitory-picture_501788393.jpg'),
            'alt' => 'Sân trường FPT Polytechnic',
            'title' => 'KÝ TÚC XÁ VaMos',
            'subtitle' => 'Không gian sống năng động – An toàn – Tiện nghi cho sinh viên'
        ],
        [
            'src' => asset('images/image-1024x682.jpg'),
            'alt' => 'Sinh viên FPT Polytechnic',
            'title' => 'CỘNG ĐỒNG SINH VIÊN NHIỆT HUYẾT',
            'subtitle' => 'Gắn kết – Sẻ chia – Cùng nhau phát triển'
        ],
        [
            'src' => asset('images/6y6ruPj73iy2ksxMA5q3A2eMmULZ9EvYEbTKdWwx.jpeg'),
            'alt' => 'Cơ sở vật chất hiện đại',
            'title' => 'CƠ SỞ VẬT CHẤT HIỆN ĐẠI',
            'subtitle' => 'Đầy đủ tiện ích phục vụ học tập và sinh hoạt'
        ],
        [
            'src' => asset('images/excel-2-2-e1713237797743.jpg'),
            'alt' => 'Hoạt động ngoại khóa',
            'title' => 'HOẠT ĐỘNG NGOẠI KHÓA SÔI NỔI',
            'subtitle' => 'Nâng cao kỹ năng – Mở rộng trải nghiệm'
        ],
    ];
@endphp
<div class="hero-section hero-slider" data-autoplay="4000">
    @foreach ($heroSlides as $index => $slide)
        <div class="hero-slide {{ $loop->first ? 'is-active' : '' }}"
            data-title="{{ $slide['title'] }}"
            data-subtitle="{{ $slide['subtitle'] }}"
            data-index="{{ $index }}">
            <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}">
        </div>
    @endforeach

    <div class="hero-content">
        <div>
            <h1 class="hero-title" id="heroTitle">{{ $heroSlides[0]['title'] }}</h1>
            <p class="hero-subtitle" id="heroSubtitle">{{ $heroSlides[0]['subtitle'] }}</p>
        </div>
    </div>

    <button class="hero-slider-nav prev" type="button" aria-label="Slide trước">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="hero-slider-nav next" type="button" aria-label="Slide tiếp theo">
        <i class="fas fa-chevron-right"></i>
    </button>

    <div class="hero-slider-controls">
        @foreach ($heroSlides as $index => $slide)
            <button class="hero-slider-dot {{ $loop->first ? 'is-active' : '' }}" type="button"
                data-index="{{ $index }}" aria-label="Chuyển đến slide {{ $index + 1 }}"></button>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.querySelector('.hero-slider');
        if (!slider) return;

        const slides = slider.querySelectorAll('.hero-slide');
        const dots = slider.querySelectorAll('.hero-slider-dot');
        const prevBtn = slider.querySelector('.hero-slider-nav.prev');
        const nextBtn = slider.querySelector('.hero-slider-nav.next');
        const titleEl = document.getElementById('heroTitle');
        const subtitleEl = document.getElementById('heroSubtitle');
        const autoplayDelay = parseInt(slider.getAttribute('data-autoplay') || '6000', 10);

        let currentIndex = 0;
        let autoplayId;

        const setSlide = (index) => {
            if (!slides.length) return;
            
            // Remove active state
            slides[currentIndex]?.classList.remove('is-active');
            dots[currentIndex]?.classList.remove('is-active');

            // Update index
            currentIndex = (index + slides.length) % slides.length;

            // Add active state with slight delay for smooth transition
            setTimeout(() => {
                const activeSlide = slides[currentIndex];
                activeSlide.classList.add('is-active');
                dots[currentIndex]?.classList.add('is-active');

                // Update text with fade effect
                if (titleEl && subtitleEl) {
                    titleEl.style.opacity = '0';
                    subtitleEl.style.opacity = '0';
                    
                    setTimeout(() => {
                        titleEl.textContent = activeSlide.dataset.title || titleEl.textContent;
                        subtitleEl.textContent = activeSlide.dataset.subtitle || subtitleEl.textContent;
                        titleEl.style.opacity = '1';
                        subtitleEl.style.opacity = '1';
                    }, 200);
                }
            }, 50);
        };

        const nextSlide = () => setSlide(currentIndex + 1);
        const prevSlide = () => setSlide(currentIndex - 1);

        const startAutoplay = () => {
            stopAutoplay();
            autoplayId = setInterval(nextSlide, autoplayDelay);
        };

        const stopAutoplay = () => {
            if (autoplayId) {
                clearInterval(autoplayId);
            }
        };

        dots.forEach(dot => {
            dot.addEventListener('click', () => {
                const targetIndex = Number(dot.dataset.index);
                if (!Number.isNaN(targetIndex)) {
                    setSlide(targetIndex);
                    startAutoplay();
                }
            });
        });

        prevBtn?.addEventListener('click', () => {
            prevSlide();
            startAutoplay();
        });

        nextBtn?.addEventListener('click', () => {
            nextSlide();
            startAutoplay();
        });

        slider.addEventListener('mouseenter', stopAutoplay);
        slider.addEventListener('mouseleave', startAutoplay);

        setSlide(0);
        startAutoplay();
    });
</script>
@endpush

@section('content')
<div class="content-section">
    <div class="container-fluid">
        <!-- Tin tức và Thông báo -->
        <div class="row mb-5" id="tin-tuc">
            <!-- Tin tức - Cột trái -->
            <div class="col-md-6" id="thong-bao">
                <div class="panel">
                <h2 class="section-title">TIN TỨC</h2>
                
                @if(isset($tinTuc) && $tinTuc->count() > 0)
                    @foreach($tinTuc->take(3) as $tin)
                    <div class="news-item">
                        <img src="{{ $tin->anh ? asset('storage/' . $tin->anh) : 'https://via.placeholder.com/120x80' }}" 
                             alt="{{ $tin->tieuDe->ten_tieu_de ?? 'Tin tức' }}" 
                             class="news-thumbnail">
                        <div class="news-content">
                            <h6>{{ $tin->tieuDe->ten_tieu_de ?? 'Tin tức' }}</h6>
                            <p>{{ Str::limit($tin->noi_dung, 100) }}</p>
                            <span class="news-date">{{ $tin->ngay_dang ? \Carbon\Carbon::parse($tin->ngay_dang)->format('d/m/Y') : '' }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Placeholder news items -->
                    <div class="news-item">
                        <img src="https://via.placeholder.com/120x80" alt="News" class="news-thumbnail">
                        <div class="news-content">
                            <h6>Sức sống chào mừng Ngày Phụ nữ Việt Nam tại Ký túc xá</h6>
                            <p>Chương trình văn nghệ đặc sắc với nhiều tiết mục hấp dẫn...</p>
                            <span class="news-date">11/10/2025</span>
                        </div>
                    </div>
                    <div class="news-item">
                        <img src="https://via.placeholder.com/120x80" alt="News" class="news-thumbnail">
                        <div class="news-content">
                            <h6>Lễ khai mạc Vòng loại khu vực phía Nam</h6>
                            <p>Giải đấu thể thao sôi động với sự tham gia của nhiều sinh viên...</p>
                            <span class="news-date">10/10/2025</span>
                        </div>
                    </div>
                    <div class="news-item">
                        <img src="https://via.placeholder.com/120x80" alt="News" class="news-thumbnail">
                        <div class="news-content">
                            <h6>Hoạt động tình nguyện tại cộng đồng</h6>
                            <p>Sinh viên ký túc xá tham gia các hoạt động tình nguyện ý nghĩa...</p>
                            <span class="news-date">09/10/2025</span>
                        </div>
                    </div>
                @endif
                
                <div class="mt-3">
                    <a href="#tin-tuc" class="view-more-link">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
            
            <!-- Thông báo - Cột phải -->
            <div class="col-md-6">
                <div class="panel">
                <h2 class="section-title">THÔNG BÁO</h2>
                
                @if(isset($thongBaos) && $thongBaos->count() > 0)
                    @foreach($thongBaos->take(5) as $tb)
                    <div class="announcement-item">
                        <div class="announcement-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="announcement-content">
                            <h6>{{ $tb->tieuDe->ten_tieu_de ?? 'Thông báo' }}</h6>
                            <span class="announcement-date">{{ $tb->ngay_dang ? \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') : '' }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <!-- Placeholder announcements -->
                    <div class="announcement-item">
                        <div class="announcement-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="announcement-content">
                            <h6>Thông báo về việc hoàn trả phí chấm dứt hợp đồng</h6>
                            <span class="announcement-date">11/10/2025</span>
                        </div>
                    </div>
                    <div class="announcement-item">
                        <div class="announcement-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="announcement-content">
                            <h6>Thông báo lịch nghỉ lễ Quốc khánh</h6>
                            <span class="announcement-date">10/10/2025</span>
                        </div>
                    </div>
                    <div class="announcement-item">
                        <div class="announcement-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="announcement-content">
                            <h6>Thông báo về việc bảo trì hệ thống điện nước</h6>
                            <span class="announcement-date">09/10/2025</span>
                        </div>
                    </div>
                @endif
                
                <div class="mt-3">
                    <a href="#thong-bao" class="view-more-link">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
        </div>
        
        <!-- Giới thiệu và Đăng ký -->
        <div class="row intro-section" id="gioi-thieu">
            <!-- Giới thiệu - Cột trái -->
            <div class="col-md-6">
                <div class="panel">
                <img src="{{ asset('images/photo-1-159188526439782241575.jpg') }}" 
                     alt="Ký túc xá" 
                     class="intro-image">
                <div class="intro-title">Giới thiệu chung</div>
                <div class="intro-text">
                    <p><strong>Ký túc xá VaMos – FPT Polytechnic</strong></p>
                    <p>Không gian sống hiện đại, an toàn với đầy đủ tiện ích học tập, sinh hoạt và kết nối cộng đồng cho sinh viên toàn trường.</p>
                </div>
                <a href="{{ route('public.about') }}" class="view-more-link">Xem thêm <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
            </div>
            
            <!-- Đăng ký và Hướng dẫn - Cột phải -->
            <div class="col-md-6">
                <div class="panel">
                <div class="text-center mb-4">
                    @if(isset($hideRegisterButton) && $hideRegisterButton)
                        {{-- Nút ẩn hoàn toàn khi hồ sơ đã duyệt --}}
                    @elseif(!isset($canRegister) || $canRegister)
                        <a href="{{ route('public.apply') }}" class="register-button">
                            <i class="fas fa-file-alt"></i>
                            ĐĂNG KÝ KÝ TÚC XÁ
                        </a>
                    @else
                        <button class="register-button" disabled title="{{ $registerMessage }}">
                            <i class="fas fa-file-alt"></i>
                            ĐĂNG KÝ KÝ TÚC XÁ
                        </button>
                        @if(!empty($registerMessage))
                            <div class="mt-2 text-muted small">{{ $registerMessage }}</div>
                        @endif
                    @endif
                </div>
                
                <div class="guide-section" id="huong-dan">
                    <div class="intro-title">HƯỚNG DẪN THỦ TỤC</div>
                    <img src="{{ asset('images/huongdan.jpg') }}" 
                         alt="Hướng dẫn" 
                         class="guide-image">
                    <a href="{{ route('public.guide') }}#huong-dan" class="view-more-link">Xem thêm <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

