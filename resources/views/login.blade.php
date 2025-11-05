<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title> Ký Túc Xá Vamos — Đăng nhập</title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html,body { height:100%; }
        body {
            display:flex;
            align-items:center;
            justify-content:center;
            background-color:#eaf3ff; /* soft sky blue */
            background-image:
                radial-gradient(#c6dafc 1.2px, transparent 1.2px),
                radial-gradient(#c6dafc 1.2px, transparent 1.2px);
            background-position: 0 0, 12px 12px;
            background-size: 24px 24px;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            padding: 1.25rem;
        }
        .auth-card { width:100%; max-width: 1140px; background: transparent; border: 0; box-shadow:none; }
        .auth-grid { display:grid; grid-template-columns: 1fr; gap: 2rem; align-items: stretch; }
        @media (min-width: 992px){ .auth-grid { grid-template-columns: 1.4fr .9fr; } }

        /* Left (illustration + copy) */
        .brand-pane { color:#1e3a8a; position:relative; }
        .brand-pane .pane-inner { padding: 1.75rem; height:100%; display:flex; flex-direction:column; justify-content:center; }
        .brand-title { font-weight:800; letter-spacing:.02em; color:#1e3a8a; }
        .brand-sub { color:#1e3a8a; opacity:.9; }
        .illus {
            width:100%;
            aspect-ratio: 16/9;
            border-radius: 16px;
            background:
                linear-gradient(180deg, rgba(255,255,255,.65), rgba(255,255,255,.65)),
                radial-gradient(circle at 20% 10%, #cfe8ff, transparent 40%),
                radial-gradient(circle at 80% 80%, #b6d6ff, transparent 40%),
                linear-gradient(135deg, #d8ecff, #c6e3ff);
            box-shadow: 0 12px 32px rgba(2,132,199,.18);
        }
        .brand-note { color:#1e3a8a; opacity:.85; }

        /* Right (card) */
        .form-pane { display:flex; align-items:center; }
        .form-box { width:100%; background:#fff; border-radius:18px; box-shadow:0 22px 60px rgba(15,23,42,.15); padding: 1.5rem; }
        @media (min-width: 576px){ .form-box { padding: 2rem; } }
        .form-heading { font-weight:800; color:#1e3a8a; text-transform:uppercase; letter-spacing:.02em; }
        .form-sub { color:#0369a1; }
        .form-photo { width:100%; height:150px; border-radius:14px; background: linear-gradient(135deg,#e0f2ff,#cfe8ff); box-shadow: inset 0 0 0 1px rgba(0,0,0,.04); margin-bottom:1rem; }

        /* Inputs */
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label { opacity:.9; transform: translateY(-0.65rem) scale(.85); }
        .form-control { border-radius:999px; background:#eaf3ff; border:1px solid #cfe0ff; }
        .form-control:focus { border-color:#0ea5e9; box-shadow:0 0 0 .2rem rgba(14,165,233,.18); background:#fff; }

        /* Buttons */
        .btn-sky { display:inline-flex; align-items:center; justify-content:center; gap:.5rem; border:none; border-radius:999px; font-weight:800; padding:.9rem 1rem; background:linear-gradient(90deg,#38bdf8,#0369a1); color:#fff; box-shadow:0 10px 28px rgba(3,105,161,.25); }
        .btn-sky:hover { color:#fff; transform: translateY(-1px); box-shadow:0 14px 36px rgba(3,105,161,.35); }
        .btn-ghost { border-radius:12px; font-weight:700; padding:.7rem 1rem; border:1px solid #e5e7eb; background:#fff; color:#111827; }
        .btn-ghost:hover { background:#f9fafb; }

        .divider { display:flex; align-items:center; gap:.75rem; color:#9ca3af; font-size:.9rem; }
        .divider::before, .divider::after { content:""; display:block; height:1px; background:#e5e7eb; flex:1; }
    </style>
</head>
<body>
<main class="auth-card">
    <div class="auth-grid">
        <div class="brand-pane">
            <div class="pane-inner">
                <div class="illus mb-4" style="background-image:url('{{ asset('images/lovepik-different-male-college-students-in-the-dormitory-picture_501788393.jpg') }}'); background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
                <h2 class="brand-title h4 mb-3">Ký Túc Xá Vamos</h2>
                <p class="brand-note mb-4">Môi trường sống tốt, học tập tốt. Kết nối, hỗ trợ và phát triển bền vững cho sinh viên.</p>
                <p class="brand-note small mb-0">© {{ date('Y') }} KTX System</p>
            </div>
        </div>
        <div class="form-pane">
            <div class="form-box w-100">
                <div class="form-photo" style="background-image:url('{{ asset('images/Gemini_Generated_Image_hxk4evhxk4evhxk4.png') }}'); background-size:cover; background-position:center; background-repeat:no-repeat;"></div>
                <div class="mb-3">
                    <h3 class="form-heading mb-1">Đăng nhập</h3>
                    <p class="form-sub mb-0">Đăng nhập vào tài khoản của bạn để tiếp tục</p>
                </div>

        <div class="col-12">
    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Hiển thị lỗi đăng nhập -->
    @if ($errors->has('access'))
        <div class="alert alert-danger">
            {{ $errors->first('access') }}
        </div>
    @endif

    @if ($errors->has('email'))
        <div class="alert alert-danger">
            {{ $errors->first('email') }}
        </div>
    @endif
</div>


        <form method="post" action="{{ route('auth.handle.login') }}" id="loginForm" class="needs-validation mt-3" novalidate>
            @csrf
            <!-- Email -->
            <div class="mb-3 form-floating">
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="name@example.com"
                    required
                    aria-describedby="emailHelp"
                    autocomplete="email"
                />
                <label for="email">Địa chỉ Email</label>
                <div class="invalid-feedback">
                    Vui lòng nhập địa chỉ email hợp lệ.
                </div>
            </div>

            <!-- Mật khẩu + nút hiển thị -->
            <div class="mb-3 position-relative form-floating">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Mật khẩu"
                    required
                    minlength="6"
                    autocomplete="current-password"
                />
                <label for="password">Mật khẩu</label>
                <button
                    type="button"
                    class="btn btn-sm btn-ghost position-absolute"
                    id="togglePassword"
                    aria-label="Hiển thị mật khẩu"
                    style="top: 0.5rem; right: 0.5rem; transform: translateY(50%);"
                >
                    Hiện
                </button>
                <div class="invalid-feedback">
                    Mật khẩu là bắt buộc (tối thiểu 6 ký tự).
                </div>
            </div>

            <!-- Ghi nhớ / Quên mật khẩu -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                    <label class="form-check-label small" for="remember">Ghi nhớ tôi</label>
                </div>
            </div>

            <!-- Nút gửi -->
            <div class="d-grid mb-3">
                <button class="btn-sky" type="submit">Đăng nhập</button>
            </div>
            <div class="divider mb-3"><span>Hoặc</span></div>
            <div class="d-grid">
                <a href="{{ route('auth.register') }}" class="btn-ghost text-center">Tạo tài khoản mới</a>
            </div>
        </form>
            </div>
        </div>
    </div>
</main>

<!-- Bootstrap JS (bundle bao gồm Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Kiểm tra form phía client bằng Bootstrap
    (function () {
        'use strict';
        const form = document.getElementById('loginForm');

        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            } else {
                event.preventDefault(); // xóa dòng này khi gắn submit thật
                // Ví dụ: hiển thị thông báo tạm thời (demo)
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerText = 'Đang đăng nhập...';
                form.submit();
            }
            form.classList.add('was-validated');
        }, false);
    })();

    // Hiển thị / Ẩn mật khẩu
    (function () {
        const pw = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');

        toggle.addEventListener('click', function () {
            const isPassword = pw.type === 'password';
            pw.type = isPassword ? 'text' : 'password';
            toggle.textContent = isPassword ? 'Ẩn' : 'Hiện';
            toggle.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
        });
    })();
</script>
</body>
</html>
