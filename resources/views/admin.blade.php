<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Quản trị viên</title>
</head>
<body>
<h1>Đây là trang dành cho quản trị viên</h1>
<h4>Xin chào quản trị viên: {{ Auth::user()->name }}</h4>
<a href="{{ route('auth.logout') }}">Đăng xuất</a>
</body>
</html>
