<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Truy cập bị từ chối</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 480px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: #dc3545;
            line-height: 1;
        }
        .error-message {
            font-size: 1.25rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div class="error-container">
    <div class="error-code">403</div>
    <h1 class="mb-3">Truy cập bị từ chối</h1>
    <p class="error-message mb-4">
        Bạn không có quyền truy cập vào trang này.<br>
        Vui lòng kiểm tra quyền hoặc quay lại trang chủ.
    </p>
    <a href="/" class="btn btn-danger px-4">
        ← Quay lại trang chủ
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
