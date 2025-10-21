<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn #{{ $hoaDon->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>HÓA ĐƠN DỊCH VỤ</h2>
    
    <p><strong>Mã hóa đơn:</strong> {{ $hoaDon->id }}</p>
    <p><strong>Sinh viên:</strong> {{ $hoaDon->sinhVien->ho_ten ?? 'Không rõ' }} (ID: {{ $hoaDon->sinh_vien_id }})</p>
    <p><strong>Mã sinh viên:</strong> {{ $hoaDon->sinh_vien_id }}</p>
    <p><strong>Loại phí:</strong> {{ $hoaDon->loai_phi }}</p>
    <p><strong>Số tiền:</strong> {{ number_format($hoaDon->so_tien, 0, ',', '.') }} đ</p>
    <p><strong>Ngày tạo:</strong> {{ $hoaDon->ngay_tao }}</p>
    <p><strong>Trạng thái:</strong> {{ $hoaDon->trang_thai }}</p>

    <table>
        <tr>
            <th>Thông tin</th>
            <th>Giá trị</th>
        </tr>
        <tr>
            <td>Loại phí</td>
            <td>{{ $hoaDon->loai_phi }}</td>
        </tr>
        <tr>
            <td>Loại phí</td>
            <td>{{ $hoaDon->loai_phi }}</td>
        </tr>
        <tr>
            <td>Số tiền</td>
            <td>{{ number_format($hoaDon->so_tien, 0, ',', '.') }} đ</td>
        </tr>
        <tr>
            <td>Ngày tạo</td>
            <td>{{ $hoaDon->ngay_tao }}</td>
        </tr>
        <tr>
            <td>Trạng thái</td>
            <td>{{ $hoaDon->trang_thai }}</td>
        </tr>
    </table>

    <p style="margin-top: 40px;">Cảm ơn bạn đã sử dụng dịch vụ!</p>
</body>
</html>
