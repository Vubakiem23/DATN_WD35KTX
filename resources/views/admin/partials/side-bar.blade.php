<div class="col-md-3 left_col">
    <div class="left_col scroll-view">
        <div class="navbar nav_title" style="border: 0;">
            <a href="{{ route('admin.index') }}" class="site_title"><i class="fa fa-paw"></i> <span>ADMIN Ký Túc Xá Vamos</span></a>
        </div>

        <div class="clearfix"></div>

        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="images/img.jpg" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Welcome,</span>
                <h2>ADMIN</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                    <li><a><i class="fa fa-home"></i> Ký Túc Xá VaMos <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li> <a href="{{ route('phong.index') }}"
                                    class="nav-link {{ request()->routeIs('phong.*') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-door-open"></i>
                                    <p>Quản lý phòng</p>
                                </a></li>
                            <li><a href="index2.html">Quản Lý Sinh Viên</a></li>
                        </ul>
                    </li>


                    <li><a><i class="fa fa-table"></i> Cơ Sở Vật Chất <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="{{ route('taisan.index') }}">Quản Lý Tài Sản , Thiết Bị Phòng</a></li>
                    
                            <li><a href="{{ route('lichbaotri.index') }}">Lịch bảo trì</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-bar-chart-o"></i> Ban Kế Toán <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="chartjs.html">Quản Lý Thu Phí , Hóa Đơn</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-clone"></i> Tiếp Nhận , Sử Lý Sự Cố , Bảo Trì<span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="fixed_sidebar.html">Quản lý yêu cầu sửa chữa, bảo trì </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="menu_section">
                <h3>Live On</h3>
                <ul class="nav side-menu">
                    <li><a><i class="fa fa-bug"></i> Thông Báo, Tin Tức <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="e_commerce.html">E-commerce</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-windows"></i> • Báo cáo, thống kê <span
                                class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="page_403.html">403 Error</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

        </div>
        <!-- /sidebar menu -->

        <!-- /menu footer buttons -->
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
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
                <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
        </div>
        <!-- /menu footer buttons -->
    </div>
</div>
