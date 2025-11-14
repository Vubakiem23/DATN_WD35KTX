<header class="main-header sticky-top">
    <!-- Top brand bar -->
    <nav class="navbar navbar-expand-md navbar-dark" style="background-color:var(--dark-blue);border-bottom:1px solid rgba(255,255,255,0.08);">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-3" href="{{ route('public.home') }}">
                <img src="{{ asset('images/Gemini_Generated_Image_hxk4evhxk4evhxk4.png') }}" alt="Ký Túc Xá Vamos" style="height:44px;width:auto;">
                <span class="d-flex flex-column lh-1">
                    <span class="fw-bold fs-5 text-uppercase">Ký túc xá VaMos</span>
                    <small class="opacity-75">Dormitory Management</small>
                </span>
            </a>

            <ul class="navbar-nav ms-auto align-items-center gap-3 d-none d-md-flex">
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" href="#" title="Tìm kiếm">
                        <i class="fas fa-search fs-5"></i>
                        <small class="small">Tìm kiếm</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" href="#" title="Ngôn ngữ">
                        <i class="fas fa-globe fs-5"></i>
                        <small class="small">English</small>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" href="#" title="Ứng dụng">
                        <i class="fas fa-list-alt fs-5"></i>
                        <small class="small">Ứng dụng</small>
                    </a>
                </li>
                @php
                    $user = auth()->user();
                    $hasStudentRole = $user?->roles?->contains('ma_quyen', 'student') ?? false;
                    $sinhVien = $user?->sinhVien;
                    $studentApproved = $hasStudentRole && $sinhVien && $sinhVien->trang_thai_ho_so === 'Đã duyệt';
                @endphp
                @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ $user?->name ?? 'Tài khoản' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @if ($hasStudentRole)
                                @if ($studentApproved)
                                    <li><a class="dropdown-item" href="{{ route('client.dashboard') }}">Tổng quan</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.phong') }}">Phòng</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.profile') }}">Hồ sơ cá nhân</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.suco.index') }}">Sự cố</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.hoadon.index') }}">Hóa đơn</a></li>
                                    <li><a class="dropdown-item" href="{{ route('client.thongbao.index') }}">Thông báo</a></li>
                                @else
                                    <li><div class="dropdown-item-text text-danger">Hồ sơ của bạn chưa được duyệt</div></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li><a class="dropdown-item" href="{{ route('auth.logout') }}">Đăng xuất</a></li>
                        </ul>
                    </li>
                @endauth
                @guest
                <li class="nav-item">
                    <a class="nav-link d-flex flex-column align-items-center" href="{{ route('auth.login') }}" title="Đăng nhập">
                        <i class="fas fa-user fs-5"></i>
                        <small class="small">Đăng nhập</small>
                    </a>
                </li>
                @endguest
            </ul>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicSecondaryNav" aria-controls="publicSecondaryNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- Secondary dark navigation -->
    <nav class="navbar navbar-expand-md navbar-dark" style="background-color:var(--dark-blue);">
        <div class="container">
            <div class="collapse navbar-collapse" id="publicSecondaryNav">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2" href="{{ route('public.home') }}">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#gioi-thieu">Giới thiệu</a></li>
                    <li class="nav-item"><a class="nav-link" href="#thong-bao">Thông báo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tin-tuc">Tin tức</a></li>
                    <li class="nav-item"><a class="nav-link" href="#huong-dan">Hướng dẫn</a></li>
                    <li class="nav-item"><a class="nav-link" href="#noi-quy">Nội quy</a></li>
                </ul>
                <ul class="navbar-nav ms-auto mb-2 mb-md-0 align-items-md-center gap-2"></ul>
            </div>
        </div>
    </nav>
</header>

