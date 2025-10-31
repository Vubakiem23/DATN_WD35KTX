<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Biên lai hóa đơn phòng {{ $hoaDon->phong->ten_phong ?? 'Không rõ' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            margin: 40px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #0d6efd;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #e9ecef;
        }
        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 15px;
        }
        .sign {
            width: 100%;
            margin-top: 60px;
            display: table;
        }
        .sign .col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sign strong {
            display: block;
            margin-bottom: 70px;
            text-transform: uppercase;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>BIÊN LAI HÓA ĐƠN THANH TOÁN</h2>
        <p><strong>Tháng:</strong> {{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</p>
    </div>

    <div class="info">
    <p><strong>Khu:</strong>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</p>
    <p><strong>Phòng:</strong> {{ $hoaDon->phong->ten_phong ?? 'Không rõ' }}</p>
    <p><strong>Loại phòng:</strong> {{ $hoaDon->phong->loai_phong ?? 'Không rõ' }}</p>
    <p><strong>Khoảng thời gian sử dụng:</strong> 
    {{ $hoaDon->created_at ? \Carbon\Carbon::parse($hoaDon->created_at)->format('d/m/Y') : '-' }} → 
    {{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}
    </p>

    <p><strong>Ngày tạo hóa đơn:</strong> {{ \Carbon\Carbon::parse($hoaDon->created_at)->format('d/m/Y') }}</p>
    <p><strong>Ngày thanh toán:</strong> 
        {{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : '-' }}
    </p>
</div>
@if($hoaDon->trang_thai === 'Đã thanh toán')
    <div style="border: 2px solid green; padding: 10px; margin-bottom: 20px; text-align: center; font-weight: bold; color: green;">
        HÓA ĐƠN ĐÃ THANH TOÁN
    </div>
@endif



    <table>
        <thead>
            <tr>
                <th>Hạng mục</th>
                <th>Chỉ số cũ</th>
                <th>Chỉ số mới</th>
                <th>Sử dụng</th>
                <th>Đơn giá (VND)</th>
                <th>Thành tiền (VND)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Điện</td>
                <td>{{ $hoaDon->so_dien_cu }}</td>
                <td>{{ $hoaDon->so_dien_moi }}</td>
                <td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td>
                <td>{{ number_format($hoaDon->don_gia_dien, 0, ',', '.') }}</td>
                <td>{{ number_format(($hoaDon->so_dien_moi - $hoaDon->so_dien_cu) * $hoaDon->don_gia_dien, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Nước</td>
                <td>{{ $hoaDon->so_nuoc_cu }}</td>
                <td>{{ $hoaDon->so_nuoc_moi }}</td>
                <td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td>
                <td>{{ number_format($hoaDon->don_gia_nuoc, 0, ',', '.') }}</td>
                <td>{{ number_format(($hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu) * $hoaDon->don_gia_nuoc, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="5" style="text-align: right;"><strong>Tiền phòng</strong></td>
                <td>{{ number_format($hoaDon->phong->gia_phong, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total">
        Tổng cộng: {{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND
    </div>

    <div class="sign">
        <div class="col">
            <strong>Người lập hóa đơn</strong>
            (Ký, ghi rõ họ tên)
        </div>
        <div class="col">
            <strong>Người thanh toán</strong>
            (Ký, ghi rõ họ tên)
        </div>
    </div>

    <div class="footer">
        <p>Cảm ơn quý khách đã thanh toán đúng hạn!</p>
    </div>
</body>
</html>
