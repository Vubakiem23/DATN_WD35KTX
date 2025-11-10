@extends('public.layouts.app')

@section('title', 'Trang chủ | KÝ TÚC XÁ')

@section('hero')
<div class="hero-section">
    <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?w=1920&h=500&fit=crop" alt="Ký túc xá TDTU" />
    <div class="hero-content">
        <div>
            <div class="hero-title">TDTU KÝ TÚC XÁ</div>
            <div class="hero-subtitle">Không gian sống năng động – An toàn – Tiện nghi cho sinh viên</div>
        </div>
    </div>
</div>
@endsection

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
                <img src="https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&h=300&fit=crop" 
                     alt="Ký túc xá" 
                     class="intro-image">
                <div class="intro-title">Giới thiệu chung</div>
                <div class="intro-text">
                    <p><strong>Trường Đại học Tôn Đức Thắng</strong></p>
                    <p>Ton Duc Thang University 2005</p>
                </div>
                <a href="#gioi-thieu" class="view-more-link">Xem thêm <i class="fas fa-arrow-right ms-1"></i></a>
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
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=600&h=200&fit=crop" 
                         alt="Hướng dẫn" 
                         class="guide-image">
                    <a href="#huong-dan" class="view-more-link">Xem thêm <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

