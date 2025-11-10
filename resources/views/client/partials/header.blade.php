<header class="client-header bg-primary text-white shadow-sm">
    <div class="container-fluid">
        <div class="row align-items-center py-3">
            <div class="col-md-3">
                <a href="{{ route('client.dashboard') }}" class="text-white text-decoration-none">
                    <h4 class="mb-0">
                        <i class="fas fa-home me-2"></i>
                        <strong>Ký Túc Xá VaMos</strong>
                    </h4>
                </a>
            </div>
            <div class="col-md-6 text-center">
                <p class="mb-0 small">Hệ thống quản lý ký túc xá dành cho sinh viên</p>
            </div>
            <div class="col-md-3 text-end">
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>
                        <span id="user-name">{{ Auth::user()->name ?? 'Sinh viên' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('client.profile') }}">
                                <i class="fas fa-user me-2"></i> Thông tin cá nhân
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('auth.logout') }}">
                                <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
