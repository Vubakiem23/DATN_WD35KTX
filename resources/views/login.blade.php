<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Đăng nhập — Bootstrap</title>

    <!-- Bootstrap 5 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* Một vài style nhỏ để căn giữa thẻ card và tạo nền nhẹ nhàng */
        html,body { height:100%; }
        body {
            display:flex;
            align-items:center;
            justify-content:center;
            background: linear-gradient(135deg,#eef2ff 0%, #ffffff 50%);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            padding: 2rem;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 6px 22px rgba(17,24,39,0.08);
        }
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            opacity: .85;
            transform: translateY(-0.65rem) scale(.85);
        }
    </style>
</head>
<body>
<main class="login-card card p-4">
    <div class="card-body">
        <div class="text-center mb-3">
            <h4 class="card-title mb-0">Chào mừng trở lại</h4>
            <p class="text-muted small mb-0">Đăng nhập vào tài khoản của bạn</p>
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
                    class="btn btn-sm btn-outline-secondary position-absolute"
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
                <button class="btn btn-primary btn-lg" type="submit">Đăng nhập</button>
            </div>

            <div class="text-center mt-3">
                <p class="small mb-2 text-muted">Chưa có tài khoản?</p>
                <a href="{{ route('auth.register') }}" class="btn btn-outline-success w-100">
                    Tạo tài khoản mới
                </a>
            </div>
            
        </form>
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
