<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <!-- Logo -->
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.index') }}" class="site_title">
                <i class="fa fa-paw"></i> <span>ADMIN Ký Túc Xá Vamos</span>
            </a>
        </div>

        <div class="clearfix"></div>

        <!-- Thông tin admin -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="{{ asset('images/img.jpg') }}" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2>ADMIN</h2>
            </div>
        </div>

        <br />

        <!-- Menu chính -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li>
    <a><i class="fa fa-users"></i> Quản Lý Người Dùng <span class="fa fa-chevron-down"></span></a>
    <ul class="nav child_menu">
        <!-- Danh sách tài khoản -->
        <li>
            <a href="{{ route('users.index') }}"
               class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa fa-list"></i> Danh sách tài khoản
            </a>
        </li>

        
    </ul>
</li>

                    <!-- Quản lý ký túc xá -->
                    <li>
                        <a><i class="fa fa-home"></i> Ký Túc Xá VaMos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('phong.index') }}"
                                    class="{{ request()->routeIs('phong.*') ? 'active' : '' }}">
                                    <i class="fa fa-door-open"></i> Quản lý phòng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sinhvien.index') }}"
                                    class="{{ request()->routeIs('sinhvien.*') ? 'active' : '' }}">
                                    <i class="fa fa-user-graduate"></i> Quản lý sinh viên
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('vipham.index') }}"
                                class="{{ request()->routeIs('taisan.*') ? 'active' : '' }}">
                                Quản lý sinh viên vi phạm
                            </a>
                            </li>
                </ul>
                </li>

                <!-- Quản lý cơ sở vật chất -->
                <li>
                    <a><i class="fa fa-table"></i> Cơ Sở Vật Chất <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li>
                            <a href="{{ route('taisan.index') }}"
                                class="{{ request()->routeIs('taisan.*') ? 'active' : '' }}">
                                Quản lý tài sản, thiết bị phòng
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lichbaotri.index') }}"
                                class="{{ request()->routeIs('lichbaotri.*') ? 'active' : '' }}">
                                Lịch bảo trì
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kho.index') }}"
                                class="{{ request()->routeIs('kho.*') ? 'active' : '' }}">
                                Kho đồ
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('loaitaisan.index') }}"
                                class="{{ request()->routeIs('loaitaisan.*') ? 'active' : '' }}">
                                Loại tài sản
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a><i class="fa fa-bar-chart-o"></i> Ban Kế Toán <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                        <li><a href="{{ route('hoadon.index') }}">Quản lý thu phí & hóa đơn</a></li>




                    </ul>

                </li>

                <li>
                    <a><i class="fa fa-wrench"></i> Tiếp Nhận, Xử Lý Sự Cố, Bảo Trì
                        <span class="fa fa-chevron-down"></span></a>

                    <ul class="nav child_menu">
                        <li>
                            <a href="{{ route('suco.index') }}"
                                class="{{ request()->routeIs('suco.*') ? 'active' : '' }}">
                                Quản lý yêu cầu sửa chữa, bảo trì
                            </a>
                        </li>

                    </ul>





                </li>
                </ul>
            </div>

            <!-- Phần khác -->
            <div class="menu_section">
                <h3>Live On</h3>
                <ul class="nav side-menu">
                    <li>
                        <a><i class="fa fa-bug"></i> Thông Báo, Tin Tức <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('thongbao.index') }}"
                                    class="{{ request()->routeIs('thongbao.*') ? 'active' : '' }}">
                                    Thông báo sự cố
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-windows"></i> Báo cáo, thống kê <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="#">Báo cáo hoạt động</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /Menu -->

        <!-- Footer -->
        <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Settings">
                <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Lock">
                <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="{{ route('auth.logout') }}">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>
