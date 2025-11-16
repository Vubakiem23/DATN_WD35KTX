<nav class="client-navbar navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#clientNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="clientNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('public.home') }}">
                        <i class="fas fa-globe me-1"></i> Trang chủ công khai
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.dashboard') ? 'active' : '' }}" 
                       href="{{ route('client.dashboard') }}">
                        <i class="fas fa-home me-1"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.phong*') ? 'active' : '' }}" 
                       href="{{ route('client.phong') }}">
                        <i class="fas fa-door-open me-1"></i> Phòng của tôi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.suco*') ? 'active' : '' }}" 
                       href="{{ route('client.suco.index') }}">
                        <i class="fas fa-tools me-1"></i> Báo sự cố
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.hoadon*') ? 'active' : '' }}" 
                       href="{{ route('client.hoadon.index') }}">
                        <i class="fas fa-file-invoice me-1"></i> Hóa đơn
                        
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('client.thongbao*') ? 'active' : '' }}" 
                       href="{{ route('client.thongbao.index') }}">
                        <i class="fas fa-bell me-1"></i> Thông báo
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
