<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/favicon.ico" type="image/ico" />

    <title>Admin Ký Túc Xá Vamos</title>

    <!-- Bootstrap -->
    <link href="{{ asset('assets/admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('assets/admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('assets/admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- iCheck -->
    <link href="{{ asset('assets/admin/vendors/iCheck/skins/flat/green.css') }}" rel="stylesheet">

    <!-- bootstrap-progressbar -->
    <link href="{{ asset('assets/admin/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}"
        rel="stylesheet">

    <!-- JQVMap -->
    <link href="{{ asset('assets/admin/vendors/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet" />
    <!-- bootstrap-daterangepicker -->
    <link href="{{ asset('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('assets/admin/build/css/custom.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/build/css/custom.css') }}" rel="stylesheet">
    <style>
        /* Global cosmetic polish */
        body {
            font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
            font-size: 15px;
        }

        h3,
        h4,
        h5 {
            font-weight: 600;
            letter-spacing: .2px;
        }

        .right_col {
            background: #f7f9fc;
        }

        .card {
            border: 1px solid #edf1f7;
            border-radius: .5rem;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid #edf1f7;
        }

        .badge {
            border-radius: 10rem;
            padding: .4rem .7rem;
            font-weight: 600
        }

        .btn {
            border-radius: .45rem;
        }

        .btn-primary {
            background: #3b82f6;
            border-color: #3b82f6
        }

        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb
        }

        .btn-secondary {
            background: #64748b;
            border-color: #64748b
        }

        .btn-secondary:hover {
            background: #475569;
            border-color: #475569
        }

        .btn-success {
            background: #10b981;
            border-color: #10b981
        }

        .btn-success:hover {
            background: #059669;
            border-color: #059669
        }

        .btn-danger {
            background: #ef4444;
            border-color: #ef4444
        }

        .btn-danger:hover {
            background: #dc2626;
            border-color: #dc2626
        }

        .table {
            margin-bottom: 0
        }

        .table th {
            color: #334155;
            font-weight: 600;
            font-size: 14px
        }

        .table td {
            color: #0f172a;
            font-size: 14px
        }

        .nav-tabs .nav-link {
            border: none;
            color: #475569
        }

        .nav-tabs .nav-link.active {
            color: #111827;
            font-weight: 600;
            border-bottom: 2px solid #3b82f6
        }

        .progress {
            background: #eef2f7
        }

        /* Toast notifications */
        .app-toast {
            position: fixed;
            right: 18px;
            bottom: 18px;
            z-index: 1060;
            display: none;
            min-width: 280px;
        }

        .app-toast .toast-body {
            display: flex;
            align-items: center;
            gap: .5rem
        }

        .app-toast .btn-close {
            margin-left: auto
        }

        .toast-success {
            background: #10b981;
            color: #fff
        }

        .toast-error {
            background: #ef4444;
            color: #fff
        }
    </style>
    <!-- css lọc-->
    <style>
        #filterModal .modal-body .form-control {
            height: 40px;
        }

        #filterModal .modal-body label {
            margin-bottom: .25rem;
        }
    </style>
    <!-- css sinh vien mới-->
    {{-- <style>
        .sv-list .sv-item {
            transition: background-color .15s ease, box-shadow .15s ease;
        }

        .sv-list .sv-item:hover {
            background-color: #f8fafc;
        }

        .sv-list .sv-actions {
            display: none;
            gap: .5rem;
        }

        /* Hiện hàng hành động phụ khi hover dòng, hoặc khi có focus trong dòng (hỗ trợ bàn phím/mobile) */
        .sv-item:hover .sv-actions,
        .sv-item:focus-within .sv-actions {
            display: flex;
        }

        /* Chuẩn hóa tất cả nút trong hàng action */
        .sv-actions .btn {
            display: inline-flex;
            /* đồng bộ <a> và <button> */
            align-items: center;
            justify-content: center;
            height: 36px;
            /* cùng chiều cao */
            padding: 0 14px;
            /* cùng padding ngang */
            line-height: 1;
            /* tránh kéo dãn */
            border-radius: 10px;
            /* cùng bo góc */
            font-weight: 500;
        }

        /* Khoảng cách đều giữa các nút */
        .sv-actions .btn+.btn,
        .sv-actions form+.btn,
        .sv-actions .btn+form {
            margin-left: 8px;
        }

        /* (Tuỳ chọn) set cùng bề rộng cho thật “đều” */
        .sv-actions .btn.equal-width {
            min-width: 140px;
            /* đổi 120–160 tuỳ thích */
        }
    </style> --}}

    <style>
        /* Bảng danh sách SV có hàng actions ẩn/hiện */
        .table-sv .sv-row-actions {
            display: none;
        }

        .table-sv tr.sv-row-main:hover+tr.sv-row-actions,
        .table-sv tr.sv-row-actions:hover,
        .table-sv tr.sv-row-main:focus-within+tr.sv-row-actions {
            display: table-row;
        }

        /* Nội dung hàng actions */
        .table-sv .sv-actions {
            display: flex;
            gap: .5rem;
            padding: .25rem 0;
        }

        .table-sv .sv-actions .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 14px;
            line-height: 1;
            border-radius: 10px;
            font-weight: 500;
        }

        .table-sv .sv-actions .btn.equal-width {
            min-width: 140px;
        }

        /* Hiệu ứng hover nhẹ cho hàng chính (giống list cũ) */
        .table-sv tr.sv-row-main:hover {
            background-color: #f8fafc;
        }

        /* Mobile: luôn hiện hàng actions để dễ bấm */
        @media (max-width: 768px) {
            .table-sv .sv-row-actions {
                display: table-row;
            }
        }
    </style>

<!-- css vi phạm-->
    <style>
        .table-violations thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 1;
        }

        .table-violations th,
        .table-violations td {
            vertical-align: middle;
        }

        .table-violations .fit {
            width: 1%;
            white-space: nowrap;
        }

        .badge-soft-warning {
            background: #fff7e6;
            color: #ad6800;
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 600;
        }

        .badge-soft-success {
            background: #e8fff3;
            color: #107154;
            border-radius: 999px;
            padding: .35rem .6rem;
            font-weight: 600;
        }

        .font-weight-600 {
            font-weight: 600;
        }

        /* filter controls height */
        .card .form-control,
        .card .form-select {
            height: 40px;
        }
    </style>











    @stack('styles')

</head>

<body class="nav-md">
    <div class="container body">
        <div class="main_container">
            @include('admin.partials.side-bar')
            @include('admin.partials.top-navigation')

            <!-- top navigation -->

            <!-- /top navigation -->

            <!-- page content -->
            <div class="right_col" role="main">
                @yield('content')
            </div>

            <!-- footer content -->

            <!-- /footer content -->

            @include('admin.partials.footer')
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/admin/vendors/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('assets/admin/vendors/fastclick/lib/fastclick.js') }}"></script>
    <!-- NProgress -->
    <script src="{{ asset('assets/admin/vendors/nprogress/nprogress.js') }}"></script>
    <!-- Chart.js -->
    <script src="{{ asset('assets/admin/vendors/Chart.js/dist/Chart.min.js') }}"></script>
    <!-- gauge.js -->
    <script src="{{ asset('assets/admin/vendors/gauge.js/dist/gauge.min.js') }}"></script>
    <!-- bootstrap-progressbar -->
    <script src="{{ asset('assets/admin/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ asset('assets/admin/vendors/iCheck/icheck.min.js') }}"></script>
    <!-- Skycons -->
    <script src="{{ asset('assets/admin/vendors/skycons/skycons.js') }}"></script>
    <!-- Flot -->
    <script src="{{ asset('assets/admin/vendors/Flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/Flot/jquery.flot.pie.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/Flot/jquery.flot.time.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/Flot/jquery.flot.stack.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/Flot/jquery.flot.resize.js') }}"></script>
    <!-- Flot plugins -->
    <script src="{{ asset('assets/admin/vendors/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/flot.curvedlines/curvedLines.js') }}"></script>
    <!-- DateJS -->
    <script src="{{ asset('assets/admin/vendors/DateJS/build/date.js') }}"></script>
    <!-- JQVMap -->
    <script src="{{ asset('assets/admin/vendors/jqvmap/dist/jquery.vmap.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>
    <!-- bootstrap-daterangepicker -->
    <script src="{{ asset('assets/admin/vendors/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/bootstrap-daterangepicker/daterangepicker.js') }}"></script>

    <!-- Custom Theme Scripts -->
    <script src="{{ asset('assets/admin/build/js/custom.min.js') }}"></script>
    <div class="toast app-toast" id="globalToast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            <span id="globalToastMsg">Thông báo</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"
                aria-label="Close"></button>
        </div>
    </div>
    <script>
        // Simple toast API: window.showToast(message, type)
        (function() {
            const toastEl = document.getElementById('globalToast');
            const toastMsg = document.getElementById('globalToastMsg');
            let bsToast;
            try {
                bsToast = new bootstrap.Toast(toastEl, {
                    delay: 4000
                });
            } catch (e) {}
            window.showToast = function(message, type) {
                if (!toastEl || !toastMsg || !bootstrap) return alert(message);
                toastEl.classList.remove('toast-success', 'toast-error');
                if (type === 'success') toastEl.classList.add('toast-success');
                if (type === 'error') toastEl.classList.add('toast-error');
                toastMsg.textContent = message || 'Thông báo';
                toastEl.style.display = 'block';
                bsToast && bsToast.show();
            };
        })();
    </script>
    @stack('scripts')
</body>

</html>
