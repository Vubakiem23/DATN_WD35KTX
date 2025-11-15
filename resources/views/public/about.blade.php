@extends('public.layouts.app')

@section('title', 'Giới thiệu ký túc xá VaMos')

@push('styles')
<style>
    .about-page {
        padding: 60px 0;
    }

    .hero-section.hero-static {
        height: 400px;
        position: relative;
        overflow: hidden;
        border-radius: 24px;
    }

    .hero-section.hero-static img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-content.text-start {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 40px;
        background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.7) 100%);
        color: white;
    }

    .hero-title {
        font-size: 48px;
        font-weight: 900;
        margin-bottom: 12px;
        text-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }

    .hero-subtitle {
        font-size: 18px;
        opacity: 0.95;
        font-weight: 400;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
        margin: 40px 0;
    }

    .stats-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        padding: 32px 24px;
        text-align: center;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--red-accent));
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    .stats-number {
        font-size: 42px;
        font-weight: 900;
        color: var(--primary-blue);
        margin-bottom: 8px;
        line-height: 1;
    }

    .stats-label {
        font-size: 14px;
        color: var(--text-700);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Introduction Section */
    .intro-section {
        margin: 50px 0;
    }

    .intro-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 24px;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .intro-image:hover {
        transform: scale(1.02);
    }

    /* Intro Gallery */
    .intro-gallery .intro-main-image .intro-image {
        height: 500px;
    }

    .intro-gallery .intro-side-image .intro-image {
        height: 240px;
    }

    .intro-gallery .intro-side-image:last-child .intro-image {
        height: 240px;
    }

    /* Intro Content Section */
    .intro-content-section .intro-content-image .intro-image {
        height: 350px;
    }

    /* Intro Image Grid */
    .intro-image-grid {
        margin-top: 60px;
    }

    .intro-grid-item {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .intro-grid-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 50px rgba(0, 0, 0, 0.2);
    }

    .intro-grid-item .intro-image {
        height: 280px;
        width: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .intro-grid-item:hover .intro-image {
        transform: scale(1.1);
    }

    .intro-grid-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.8) 100%);
        padding: 24px;
        color: white;
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    .intro-grid-item:hover .intro-grid-overlay {
        transform: translateY(0);
    }

    .intro-grid-overlay span {
        font-size: 18px;
        font-weight: 700;
        display: block;
    }

    .intro-badge {
        display: inline-block;
        padding: 8px 20px;
        background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));
        color: white;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }

    .intro-content h2 {
        font-size: 36px;
        font-weight: 900;
        color: var(--text-900);
        margin-bottom: 24px;
        line-height: 1.2;
    }

    .intro-content p {
        font-size: 16px;
        line-height: 1.8;
        color: var(--text-700);
        margin-bottom: 16px;
    }

    /* History Timeline */
    .history-timeline {
        position: relative;
        padding: 40px 0;
    }

    .history-timeline::before {
        content: '';
        position: absolute;
        left: 30px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--primary-blue), var(--red-accent));
    }

    .timeline-item {
        position: relative;
        padding-left: 80px;
        margin-bottom: 48px;
        animation: fadeInUp 0.6s ease forwards;
        opacity: 0;
    }

    .timeline-item:nth-child(1) { animation-delay: 0.1s; }
    .timeline-item:nth-child(2) { animation-delay: 0.2s; }
    .timeline-item:nth-child(3) { animation-delay: 0.3s; }
    .timeline-item:nth-child(4) { animation-delay: 0.4s; }
    .timeline-item:nth-child(5) { animation-delay: 0.5s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .timeline-dot {
        position: absolute;
        left: 18px;
        top: 8px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--primary-blue);
        border: 4px solid white;
        box-shadow: 0 0 0 4px var(--primary-blue), 0 4px 12px rgba(0,0,0,0.15);
        z-index: 2;
    }

    .timeline-year {
        font-size: 20px;
        font-weight: 900;
        color: var(--primary-blue);
        margin-bottom: 8px;
    }

    .timeline-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-900);
        margin-bottom: 12px;
    }

    .timeline-description {
        font-size: 15px;
        line-height: 1.7;
        color: var(--text-700);
    }

    .timeline-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .timeline-card:hover {
        transform: translateX(8px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Values Section */
    .value-list {
        list-style: none;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .value-list li {
        padding: 20px 24px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border-radius: 16px;
        border-left: 4px solid var(--primary-blue);
        font-size: 15px;
        font-weight: 600;
        color: var(--text-700);
        transition: all 0.3s ease;
    }

    .value-list li:hover {
        transform: translateX(8px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .value-list li i {
        color: #10b981;
        margin-right: 12px;
        font-size: 18px;
    }

    /* Highlights Grid */
    .highlights-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 28px;
    }

    .highlight-card {
        background: white;
        border-radius: 20px;
        padding: 32px 28px;
        text-align: center;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .highlight-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-blue), var(--red-accent));
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .highlight-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 16px 50px rgba(0, 0, 0, 0.15);
    }

    .highlight-card:hover::before {
        transform: scaleX(1);
    }

    .highlight-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 24px;
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1), rgba(220, 20, 60, 0.1));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s ease;
    }

    .highlight-card:hover .highlight-icon {
        transform: scale(1.1) rotate(5deg);
        background: linear-gradient(135deg, var(--primary-blue), var(--red-accent));
    }

    .highlight-icon i {
        font-size: 36px;
        color: var(--primary-blue);
        transition: color 0.4s ease;
    }

    .highlight-card:hover .highlight-icon i {
        color: white;
    }

    .highlight-card h5 {
        font-size: 20px;
        font-weight: 800;
        color: var(--text-900);
        margin-bottom: 16px;
    }

    .highlight-card p {
        font-size: 14px;
        line-height: 1.7;
        color: var(--text-700);
        margin: 0;
    }

    /* Guide Timeline */
    .guide-timeline {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-top: 32px;
    }

    .guide-step {
        background: white;
        border-radius: 20px;
        padding: 28px 24px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .guide-step:hover {
        transform: translateY(-6px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .guide-step-number {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, var(--primary-blue), var(--red-accent));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 900;
        margin-bottom: 16px;
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .guide-step p {
        font-size: 15px;
        line-height: 1.7;
        color: var(--text-700);
        margin: 0;
    }

    /* Rules Section */
    .rules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .rule-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        overflow: hidden;
    }

    .rule-card h5 {
        font-size: 18px;
        font-weight: 800;
        color: var(--text-900);
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary-blue);
    }

    .rule-card ul {
        list-style: none;
        padding: 0;
    }

    .rule-card ul li {
        padding: 12px 0;
        padding-left: 28px;
        position: relative;
        font-size: 14px;
        line-height: 1.7;
        color: var(--text-700);
        border-bottom: 1px solid var(--border);
    }

    .rule-card ul li:last-child {
        border-bottom: none;
    }

    .rule-card ul li::before {
        content: '✓';
        position: absolute;
        left: 0;
        color: var(--primary-blue);
        font-weight: 900;
        font-size: 16px;
    }

    /* Contact Section */
    .contact-section {
        background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary-blue) 100%);
        border-radius: 24px;
        padding: 48px;
        color: white;
        margin-top: 40px;
        overflow: hidden;
        position: relative;
    }

    .contact-section h3 {
        color: white;
        margin-bottom: 32px;
    }

    .contact-section h3::after {
        background: white;
    }

    .contact-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .contact-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .contact-icon {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .contact-icon i {
        font-size: 20px;
        color: white;
    }

    .contact-text {
        flex: 1;
    }

    .contact-text strong {
        display: block;
        margin-bottom: 4px;
        font-size: 14px;
        opacity: 0.9;
    }

    .contact-text span {
        font-size: 16px;
        font-weight: 600;
    }

    .view-more-link {
        color: var(--primary-blue);
        text-decoration: none;
        font-weight: 700;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        margin-top: 20px;
    }

    .view-more-link:hover {
        color: var(--dark-blue);
        gap: 12px;
    }

    .view-more-link i {
        transition: transform 0.3s ease;
    }

    .view-more-link:hover i {
        transform: translateX(4px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 32px;
        }

        .hero-subtitle {
            font-size: 16px;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }

        .stats-number {
            font-size: 32px;
        }

        .history-timeline::before {
            left: 15px;
        }

        .timeline-item {
            padding-left: 50px;
        }

        .timeline-dot {
            left: 3px;
            width: 20px;
            height: 20px;
        }

        .highlights-grid {
            grid-template-columns: 1fr;
        }

        .contact-section {
            padding: 32px 24px;
        }

        .intro-gallery .intro-main-image .intro-image {
            height: 300px;
        }

        .intro-gallery .intro-side-image .intro-image {
            height: 180px;
        }

        .intro-content-section .intro-content-image .intro-image {
            height: 250px;
        }

        .intro-grid-item .intro-image {
            height: 220px;
        }

        .intro-header h2 {
            font-size: 28px;
        }

        .intro-content h3 {
            font-size: 24px;
        }
    }
</style>
@endpush


@section('content')
<div class="content-section about-page">
    <div class="container">
        <!-- Introduction Section -->
        <section id="gioi-thieu" class="intro-section">
            <div class="intro-header text-center mb-5">
                <span class="intro-badge">Ký túc xá VaMos</span>
                <h2 class="mt-3 mb-4">Ngôi nhà thứ hai của sinh viên FPT Polytechnic</h2>
                <p class="lead mx-auto" style="max-width: 800px; font-size: 18px; color: var(--text-700);">
                    Ký túc xá VaMos được thiết kế như một khu phức hợp hiện đại với 4 khu nhà, hơn 1.200 giường nội trú, hệ thống tiện ích đa dạng giúp sinh viên phát triển toàn diện về học tập lẫn kỹ năng sống.
                </p>
            </div>

            <!-- Main Image Gallery -->
            <div class="intro-gallery mb-5">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="intro-main-image">
                            <img src="{{ asset('images/lovepik-different-male-college-students-in-the-dormitory-picture_501788393.jpg') }}" 
                                 alt="Phòng ở ký túc xá" 
                                 class="intro-image">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="intro-side-images">
                            <div class="intro-side-image mb-3">
                                <img src="{{ asset('images/image-1024x682.jpg') }}" 
                                     alt="Khuôn viên ký túc xá" 
                                     class="intro-image">
                            </div>
                            <div class="intro-side-image">
                                <img src="{{ asset('images/Gemini_Generated_Image_hxk4evhxk4evhxk4.png') }}" 
                                     alt="Không gian sinh hoạt" 
                                     class="intro-image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content with Images -->
            <div class="intro-content-section">
                <div class="row g-5 align-items-center mb-5">
                    <div class="col-lg-6">
                        <div class="intro-content">
                            <h3 class="mb-4" style="font-size: 28px; font-weight: 800; color: var(--text-900);">
                                Môi trường sống hiện đại và an toàn
                            </h3>
                            <p style="font-size: 16px; line-height: 1.8; color: var(--text-700); margin-bottom: 20px;">
                                Chúng tôi đặt trọng tâm vào sự an toàn, tính cộng đồng và trải nghiệm sống thông minh. Mọi hoạt động vận hành đều ứng dụng công nghệ quản lý, giúp sinh viên và phụ huynh yên tâm theo dõi thông tin mọi lúc, mọi nơi.
                            </p>
                            <p style="font-size: 16px; line-height: 1.8; color: var(--text-700);">
                                Hệ thống an ninh 24/7 với camera giám sát, kiểm soát ra vào thông minh và đội ngũ bảo vệ chuyên nghiệp đảm bảo môi trường sống an toàn tuyệt đối cho mọi sinh viên.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="intro-content-image">
                            <img src="{{ asset('images/excel-2-2-e1713237797743.jpg') }}" 
                                 alt="Cơ sở vật chất hiện đại" 
                                 class="intro-image">
                        </div>
                    </div>
                </div>

                <div class="row g-5 align-items-center">
                    <div class="col-lg-6 order-lg-2">
                        <div class="intro-content">
                            <h3 class="mb-4" style="font-size: 28px; font-weight: 800; color: var(--text-900);">
                                Cộng đồng gắn kết và phát triển
                            </h3>
                            <p style="font-size: 16px; line-height: 1.8; color: var(--text-700); margin-bottom: 20px;">
                                Với môi trường sống năng động, cơ sở vật chất hiện đại và đội ngũ quản lý chuyên nghiệp, VaMos không chỉ là nơi ở mà còn là nơi nuôi dưỡng tài năng, kết nối cộng đồng và tạo nên những kỷ niệm đáng nhớ trong hành trình đại học của bạn.
                            </p>
                            <p style="font-size: 16px; line-height: 1.8; color: var(--text-700);">
                                Hơn 20 câu lạc bộ đa dạng từ thể thao, nghệ thuật đến học thuật, cùng các chương trình mentoring và hoạt động ngoại khóa thường xuyên giúp sinh viên phát triển toàn diện cả về học tập và kỹ năng sống.
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6 order-lg-1">
                        <div class="intro-content-image">
                            <img src="{{ asset('images/6y6ruPj73iy2ksxMA5q3A2eMmULZ9EvYEbTKdWwx.jpeg') }}" 
                                 alt="Hoạt động cộng đồng" 
                                 class="intro-image">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Grid -->
            <div class="intro-image-grid mt-5">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="intro-grid-item">
                            <img src="{{ asset('images/lovepik-different-male-college-students-in-the-dormitory-picture_501788393.jpg') }}" 
                                 alt="Phòng ở" 
                                 class="intro-image">
                            <div class="intro-grid-overlay">
                                <span>Phòng ở tiện nghi</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="intro-grid-item">
                            <img src="{{ asset('images/image-1024x682.jpg') }}" 
                                 alt="Khuôn viên" 
                                 class="intro-image">
                            <div class="intro-grid-overlay">
                                <span>Khuôn viên xanh</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="intro-grid-item">
                            <img src="{{ asset('images/Gemini_Generated_Image_hxk4evhxk4evhxk4.png') }}" 
                                 alt="Không gian chung" 
                                 class="intro-image">
                            <div class="intro-grid-overlay">
                                <span>Không gian sinh hoạt</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="stats-grid">
            <div class="stats-card">
                <div class="stats-number">{{ number_format($stats['rooms']) }}</div>
                <div class="stats-label">Phòng nội trú</div>
            </div>
            <div class="stats-card">
                <div class="stats-number">{{ number_format($stats['areas']) }}</div>
                <div class="stats-label">Khu nhà</div>
            </div>
            <div class="stats-card">
                <div class="stats-number">{{ number_format($stats['students']) }}</div>
                <div class="stats-label">Sinh viên đang ở</div>
            </div>
            <div class="stats-card">
                <div class="stats-number">{{ number_format($stats['activities']) }}+</div>
                <div class="stats-label">Hoạt động mỗi năm</div>
            </div>
        </section>

        <!-- History Timeline Section -->
        <section class="panel mb-5">
            <h3 class="section-title">Lịch sử hình thành</h3>
            <div class="history-timeline">
                @foreach ($history as $item)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-card">
                        <div class="timeline-year">{{ $item['year'] }}</div>
                        <div class="timeline-title">{{ $item['title'] }}</div>
                        <div class="timeline-description">{{ $item['description'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- Values Section -->
        <section class="panel mb-5">
            <h3 class="section-title">Giá trị cốt lõi</h3>
            <ul class="value-list">
                @foreach ($values as $value)
                    <li><i class="fas fa-check-circle"></i>{{ $value }}</li>
                @endforeach
            </ul>
        </section>

        <!-- Highlights Section -->
        <section class="panel mb-5">
            <h3 class="section-title">Lợi thế nổi bật</h3>
            <div class="highlights-grid">
                @foreach ($highlights as $highlight)
                    <div class="highlight-card">
                        <div class="highlight-icon">
                            <i class="fas {{ $highlight['icon'] }}"></i>
                        </div>
                        <h5>{{ $highlight['title'] }}</h5>
                        <p>{{ $highlight['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- View More Link -->
        <div class="text-center mt-5">
            <a href="{{ route('public.guide') }}" class="view-more-link">
                Xem thêm hướng dẫn thủ tục
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
@endsection
