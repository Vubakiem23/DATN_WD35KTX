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
                                    <i class="fa fa-bed"></i> Quản lý phòng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('khu.index') }}"
                                    class="{{ request()->routeIs('khu.*') ? 'active' : '' }}">
                                    <i class="fa fa-building"></i> Quản lý khu
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sinhvien.index') }}"
                                    class="{{ request()->routeIs('sinhvien.*') ? 'active' : '' }}">
                                    <i class="fa fa-users"></i> Quản lý sinh viên
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('vipham.index') }}"
                                    class="{{ request()->routeIs('vipham.*') ? 'active' : '' }}">
                                    <i class="fa fa-exclamation-triangle"></i> Quản lý sinh viên vi phạm
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
                                    <i class="fa fa-cubes"></i> Quản lý tài sản, thiết bị phòng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lichbaotri.index') }}"
                                    class="{{ request()->routeIs('lichbaotri.*') ? 'active' : '' }}">
                                    <i class="fa fa-calendar-check-o"></i> Lịch bảo trì
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kho.index') }}"
                                    class="{{ request()->routeIs('kho.*') ? 'active' : '' }}">
                                    <i class="fa fa-archive"></i> Kho đồ
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('loaitaisan.index') }}"
                                    class="{{ request()->routeIs('loaitaisan.*') ? 'active' : '' }}">
                                    <i class="fa fa-tags"></i> Loại tài sản
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hoadonbaotri.index') }}"
                                    class="{{ request()->routeIs('hoadonbaotri.*') ? 'active' : '' }}">
                                    <i class="fa fa-tags"></i> Hóa đơn bảo trì
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a><i class="fa fa-bar-chart-o"></i> Ban Kế Toán <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('hoadon.index') }}"
                                    class="{{ request()->routeIs('hoadon.index') ? 'active' : '' }}">
                                    <i class="fa fa-bed"></i> Quản lý tiền phòng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hoadon.diennuoc') }}"
                                    class="{{ request()->routeIs('hoadon.diennuoc') ? 'active' : '' }}">
                                    <i class="fa fa-bolt"></i> Quản lý điện & nước
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('payment-confirmation.index') }}"
                                    class="{{ request()->routeIs('payment-confirmation.*') ? 'active' : '' }}">
                                    <i class="fa fa-check-circle"></i> Xác nhận thanh toán sinh viên
                                </a>
                            </li>
                        </ul>

                    </li>

                    <li>
                        <a><i class="fa fa-wrench"></i> Tiếp Nhận, Xử Lý Sự Cố, Bảo Trì
                            <span class="fa fa-chevron-down"></span></a>

                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('suco.index') }}"
                                    class="{{ request()->routeIs('suco.*') ? 'active' : '' }}">
                                    <i class="fa fa-wrench"></i> Quản lý yêu cầu sửa chữa, bảo trì
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hoadonsuco.index') }}"
                                    class="{{ request()->routeIs('hoadonsuco.*') ? 'active' : '' }}">
                                    <i class="fa fa-file-text-o"></i> Hóa đơn sự cố
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
                    <!-- Thông Báo & Tin Tức -->
                    <li>
                        <a><i class="fa fa-bullhorn"></i> Thông Báo & Tin Tức <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('thongbao.index') }}"
                                    class="{{ request()->routeIs('thongbao.*') ? 'active' : '' }}">
                                    <i class="fa fa-bell"></i> Thông báo chung
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tintuc.index') }}"
                                    class="{{ request()->routeIs('tintuc.*') ? 'active' : '' }}">
                                    <i class="fa fa-newspaper-o"></i> Tin tức
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hashtags.index') }}"
                                    class="{{ request()->routeIs('hashtags.*') ? 'active' : '' }}">
                                    <i class="fa fa-tags"></i> Quản lý Hashtag
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Thanh Toán -->
                    <li>
                        <a><i class="fa fa-credit-card"></i> Thanh Toán <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('hoadonslot.index') }}"
                                    class="{{ request()->routeIs('hoadonslot.*') ? 'active' : '' }}">
                                    <i class="fa fa-credit-card"></i> Hóa đơn Slot
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('hoadon_dien_nuoc.index') }}"
                                    class="{{ request()->routeIs('hoadon_dien_nuoc.*') ? 'active' : '' }}">
                                    <i class="fa fa-bolt"></i> Hóa đơn Điện - Nước
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Thông Báo Sự Cố -->
                    <li>
                        <a><i class="fa fa-bug"></i> Thông Báo Sự Cố, Khu, Phòng, Sinh Viên <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('thongbao_su_co.index') }}"
                                    class="{{ request()->routeIs('thongbao_su_co.*') ? 'active' : '' }}">
                                    <i class="fa fa-exclamation-triangle"></i> Thông báo sự cố
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('thongbao_khu_phong') }}"
                                    class="{{ request()->routeIs('thongbao_khu_phong') ? 'active' : '' }}">
                                    <i class="fa fa-building"></i> Thông báo khu/phòng
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('thongbao_sinh_vien.index') }}"
                                    class="{{ request()->routeIs('thongbao_sinh_vien.*') ? 'active' : '' }}">
                                    <i class="fa fa-user"></i> Thông báo sinh viên
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Báo cáo, thống kê -->
                    <li>
                        <a><i class="fa fa-windows"></i> Báo cáo, thống kê <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li>
                                <a href="{{ route('admin.index') }}"
                                    class="{{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                                    <i class="fa fa-bar-chart"></i> Báo cáo thống kê
                                </a>
                            </li>
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