<header class="main-header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo & Brand -->
            <a class="navbar-brand" href="{{ route('public.home') }}">
                <div class="brand-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="Ký Túc Xá VaMos">
                </div>
                <div class="brand-info">
                    <h1 class="brand-title">Ký Túc Xá VaMos</h1>
                    <p class="brand-subtitle">Hệ thống quản lý ký túc xá</p>
                </div>
            </a>

            <!-- Mobile Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Navbar Content -->
            <div class="collapse navbar-collapse" id="navbarContent">
                <!-- Main Navigation -->
                <ul class="navbar-nav main-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.home') ? 'active' : '' }}" href="{{ route('public.home') }}">
                            <i class="fas fa-home"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}" href="{{ route('public.about') }}">
                            <i class="fas fa-info-circle"></i>
                            <span>Giới thiệu</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.thongbao.*') ? 'active' : '' }}" href="{{ route('public.thongbao.index') }}">
                            <i class="fas fa-bullhorn"></i>
                            <span>Thông báo</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.tintuc.*') ? 'active' : '' }}" href="{{ route('public.tintuc.index') }}">
                            <i class="fas fa-newspaper"></i>
                            <span>Tin tức</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.guide') ? 'active' : '' }}" href="{{ route('public.guide') }}">
                            <i class="fas fa-book"></i>
                            <span>Hướng dẫn</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('public.rules') ? 'active' : '' }}" href="{{ route('public.rules') }}">
                            <i class="fas fa-clipboard-list"></i>
                            <span>Nội quy</span>
                        </a>
                    </li>
                </ul>

                <!-- User Actions -->
                <div class="navbar-actions">
                    @php
                        $user = auth()->user();
                        $hasStudentRole = $user?->roles?->contains('ma_quyen', 'student') ?? false;
                        $sinhVien = $user?->sinhVien;
                        $studentApproved = $hasStudentRole && $sinhVien && $sinhVien->trang_thai_ho_so === 'Đã duyệt';
                    @endphp

                    @auth
                        <div class="user-dropdown">
                            <button class="user-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <span class="user-name">{{ $user?->name ?? 'Tài khoản' }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if ($hasStudentRole)
                                    @if ($studentApproved)
                                        <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">
                                            <i class="fas fa-home"></i> Tổng quan
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('client.phong') }}">
                                            <i class="fas fa-door-open"></i> Phòng
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('client.profile') }}">
                                            <i class="fas fa-user"></i> Hồ sơ
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('client.suco.index') }}">
                                            <i class="fas fa-exclamation-triangle"></i> Sự cố
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('client.hoadon.index') }}">
                                            <i class="fas fa-receipt"></i> Hóa đơn
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('client.thongbao.index') }}">
                                            <i class="fas fa-bell"></i> Thông báo
                                        </a></li>
                                    @else
                                        <li><div class="dropdown-item-text text-muted">
                                            <i class="fas fa-info-circle"></i> Hồ sơ chưa được duyệt
                                        </div></li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                @endif
                                <li><a class="dropdown-item text-danger" href="{{ route('auth.logout') }}">
                                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                                </a></li>
                            </ul>
                        </div>
                    @else
                        <a class="btn-login" href="{{ route('auth.login') }}">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Đăng nhập</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
</header>

<style>
    /* ===== MAIN HEADER ===== */
    .main-header {
        position: sticky;
        top: 0;
        z-index: 1030;
        background: linear-gradient(135deg, #1a237e 0%, #283593 100%);
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.15);
        border-bottom: 3px solid rgba(0, 102, 204, 0.3);
    }

    .main-header .navbar {
        padding: 0;
    }

    .main-header .container {
        padding: 0 15px;
    }

    /* ===== BRAND SECTION ===== */
    .navbar-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        padding: 14px 0;
        transition: transform 0.3s ease;
    }

    .navbar-brand:hover {
        transform: translateX(2px);
    }

    .brand-logo {
        width: 56px;
        height: 56px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .navbar-brand:hover .brand-logo {
        background: rgba(255, 255, 255, 0.18);
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
    }

    .brand-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
    }

    .brand-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .brand-title {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        margin: 0;
        line-height: 1.2;
        letter-spacing: 0.3px;
    }

    .brand-subtitle {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.85);
        margin: 0;
        line-height: 1.2;
        font-weight: 400;
    }

    /* ===== MAIN NAVIGATION ===== */
    .main-nav {
        flex: 1;
        justify-content: center;
        gap: 4px;
        margin: 0 30px;
    }

    .main-nav .nav-item {
        position: relative;
    }

    .main-nav .nav-link {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
        padding: 16px 18px !important;
        transition: all 0.3s ease;
        position: relative;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        border-radius: 8px;
        margin: 0 2px;
    }

    .main-nav .nav-link i {
        font-size: 14px;
        width: 18px;
        text-align: center;
    }

    .main-nav .nav-link::after {
        content: '';
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%) scaleX(0);
        width: 70%;
        height: 2px;
        background: linear-gradient(90deg, #00a8ff, #0066cc);
        transition: transform 0.3s ease;
        border-radius: 2px;
    }

    .main-nav .nav-link:hover {
        color: #fff;
        background: rgba(255, 255, 255, 0.1);
    }

    .main-nav .nav-link:hover::after,
    .main-nav .nav-link.active::after {
        transform: translateX(-50%) scaleX(1);
    }

    .main-nav .nav-link.active {
        color: #fff;
        background: rgba(255, 255, 255, 0.12);
    }

    /* ===== USER ACTIONS ===== */
    .navbar-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-left: auto;
    }

    /* User Dropdown */
    .user-dropdown {
        position: relative;
    }

    .user-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        outline: none;
    }

    .user-btn:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .user-name {
        font-weight: 500;
    }

    .user-btn i:last-child {
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .user-btn[aria-expanded="true"] i:last-child {
        transform: rotate(180deg);
    }

    /* Login Button */
    .btn-login {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        color: #fff !important;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 102, 204, 0.3);
    }

    .btn-login:hover {
        background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
        color: #fff !important;
    }

    /* ===== DROPDOWN MENU ===== */
    .dropdown-menu {
        border: none;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        padding: 8px;
        margin-top: 8px;
        background: #fff;
        min-width: 240px;
    }

    .dropdown-item {
        padding: 11px 16px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        color: #333;
        font-size: 14px;
        text-decoration: none;
    }

    .dropdown-item i {
        width: 18px;
        text-align: center;
        color: #0066cc;
        font-size: 14px;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(0, 102, 204, 0.1) 0%, rgba(0, 102, 204, 0.05) 100%);
        color: #0066cc;
        transform: translateX(4px);
    }

    .dropdown-item.text-danger {
        color: #dc3545;
    }

    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
        color: #dc3545;
    }

    .dropdown-item.text-danger i {
        color: #dc3545;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-color: rgba(0, 0, 0, 0.1);
    }

    /* ===== MOBILE TOGGLE ===== */
    .navbar-toggler {
        border: none;
        padding: 6px;
        width: 40px;
        height: 40px;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .navbar-toggler:hover {
        background: rgba(255, 255, 255, 0.15);
    }

    .navbar-toggler span {
        display: block;
        height: 2.5px;
        width: 100%;
        background: #fff;
        border-radius: 2px;
        transition: all 0.3s ease;
    }

    .navbar-toggler[aria-expanded="true"] span:nth-child(1) {
        transform: rotate(45deg) translate(7px, 7px);
    }

    .navbar-toggler[aria-expanded="true"] span:nth-child(2) {
        opacity: 0;
    }

    .navbar-toggler[aria-expanded="true"] span:nth-child(3) {
        transform: rotate(-45deg) translate(7px, -7px);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991px) {
        .main-header .container {
            padding: 0 20px;
        }

        .navbar-brand {
            padding: 12px 0;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
        }

        .brand-title {
            font-size: 16px;
        }

        .brand-subtitle {
            font-size: 10px;
        }

        .main-nav {
            margin: 0;
            padding: 12px 0;
            flex-direction: column;
            gap: 4px;
        }

        .main-nav .nav-link {
            padding: 14px 20px !important;
            border-radius: 8px;
            margin: 2px 0;
        }

        .main-nav .nav-link::after {
            display: none;
        }

        .main-nav .nav-link.active,
        .main-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .navbar-actions {
            margin: 12px 0;
            width: 100%;
            justify-content: flex-start;
        }

        .user-btn,
        .btn-login {
            width: 100%;
            justify-content: center;
        }

        .dropdown-menu {
            width: 100%;
            margin-top: 8px;
        }
    }

    @media (max-width: 576px) {
        .brand-title {
            font-size: 15px;
        }

        .brand-subtitle {
            font-size: 9px;
        }

        .brand-logo {
            width: 44px;
            height: 44px;
        }
    }
</style>