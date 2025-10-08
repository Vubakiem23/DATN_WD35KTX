<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Đăng ký — Bootstrap</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f9fafb 0%, #ffffff 50%);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            padding: 2rem;
        }

        .register-card {
            max-width: 460px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 6px 22px rgba(17, 24, 39, 0.08);
        }
    </style>
</head>
<body>
<main class="register-card card p-4">
    <div class="card-body">
        <div class="text-center mb-3">
            <h4 class="card-title mb-0">Tạo tài khoản</h4>
            <p class="text-muted small mb-0">Vui lòng điền thông tin bên dưới</p>
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

        <form method="post" action="{{ route('auth.handle.register') }}" id="registerForm" class="needs-validation mt-3"
              novalidate>
            @csrf
            <!-- Họ và tên -->
            <div class="mb-3 form-floating">
                <input
                    type="text"
                    class="form-control"
                    id="name"
                    name="name"
                    placeholder="Họ và tên"
                    required
                    autocomplete="name"
                />
                <label for="name">Họ và tên</label>
                <div class="invalid-feedback">Vui lòng nhập họ và tên của bạn.</div>
            </div>

            <!-- Email -->
            <div class="mb-3 form-floating">
                <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="name@example.com"
                    required
                    autocomplete="email"
                />
                <label for="email">Địa chỉ Email</label>
                <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
            </div>

            <!-- Mật khẩu -->
            <div class="mb-3 form-floating position-relative">
                <input
                    type="password"
                    class="form-control"
                    id="password"
                    name="password"
                    placeholder="Mật khẩu"
                    required
                    minlength="6"
                    autocomplete="new-password"
                />
                <label for="password">Mật khẩu</label>
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary position-absolute"
                    id="togglePassword"
                    style="top: 0.5rem; right: 0.5rem; transform: translateY(50%);"
                >
                    Hiện
                </button>
                <div class="invalid-feedback">
                    Mật khẩu phải có ít nhất 6 ký tự.
                </div>
            </div>

            <!-- Xác nhận mật khẩu -->
            <div class="mb-3 form-floating">
                <input
                    type="password"
                    class="form-control"
                    id="password_confirm"
                    name="password_confirm"
                    placeholder="Xác nhận mật khẩu"
                    required
                />
                <label for="password_confirm">Xác nhận mật khẩu</label>
                <div class="invalid-feedback">Mật khẩu xác nhận không khớp.</div>
            </div>

            <!-- Điều khoản -->
            <div class="form-check mb-3">
                <input
                    class="form-check-input"
                    type="checkbox"
                    id="terms"
                    name="terms"
                    required
                />
                <label class="form-check-label small" for="terms">
                    Tôi đồng ý với <a href="#" class="link-primary">Điều khoản dịch vụ</a>
                    và <a href="#" class="link-primary">Chính sách bảo mật</a>.
                </label>
                <div class="invalid-feedback">Bạn cần đồng ý trước khi đăng ký.</div>
            </div>

            <!-- Nút Đăng ký -->
            <div class="d-grid mb-3">
                <button class="btn btn-primary btn-lg" type="submit">Tạo tài khoản</button>
            </div>
        </form>
    </div>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Kiểm tra form phía client bằng Bootstrap
    (function () {
        'use strict';
        const form = document.getElementById('registerForm');

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const password = document.getElementById('password');
            const password_confirm = document.getElementById('password_confirm');

            if (password.value !== password_confirm.value) {
                password_confirm.setCustomValidity('Mật khẩu không khớp');
            } else {
                password_confirm.setCustomValidity('');
            }

            if (form.checkValidity()) {
                // Thay bằng xử lý thực tế (backend)
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.innerText = 'Đang tạo...';
                form.submit();
            }

            form.classList.add('was-validated');
        }, false);
    })();

    // Hiện / Ẩn mật khẩu
    (function () {
        const pw = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        toggle.addEventListener('click', function () {
            const isPassword = pw.type === 'password';
            pw.type = isPassword ? 'text' : 'password';
            toggle.textContent = isPassword ? 'Ẩn' : 'Hiện';
        });
    })();
</script>
</body>
</html>
