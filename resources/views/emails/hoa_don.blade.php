<h2>Xin chào {{ $sinhVien->ho_ten }}</h2>
<p>Đây là hóa đơn tiền phòng tháng <strong>{{ \Carbon\Carbon::parse($hoaDon->created_at)->format('m/Y') }}</strong>:</p>

<table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
    <thead>
        <tr style="background-color: #f2f2f2;">
            <th>Thông tin</th>
            <th>Giá trị</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Khu</td>
            <td>{{ optional($hoaDon->phong->khu)->ten_khu ?? 'Không rõ khu' }}</td>
        </tr>
        <tr>
            <td>Phòng</td>
            <td>{{ $hoaDon->phong->ten_phong }}</td>
        </tr>
        <tr>
            <td>Loại Phòng</td>
            <td>{{ optional($hoaDon->phong)->loai_phong ?? 'Không rõ' }}</td>
        </tr>
        <tr>
            <td>Điện đã dùng</td>
            <td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td>
        </tr>
        <tr>
            <td>Nước đã dùng</td>
            <td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td>
        </tr>
        <tr>
            <td>Đơn giá điện</td>
            <td>{{ number_format($hoaDon->don_gia_dien) }} VND</td>
        </tr>
        <tr>
            <td>Đơn giá nước</td>
            <td>{{ number_format($hoaDon->don_gia_nuoc) }} VND</td>
        </tr>
        <tr>
            <td>Tiền phòng</td>
            <td>{{ number_format($hoaDon->phong->gia_phong) }} VND</td>
        </tr>
        <tr>
            <td>Tiền điện</td>
            <td>{{ number_format(($hoaDon->so_dien_moi - $hoaDon->so_dien_cu) * $hoaDon->don_gia_dien) }} VND</td>
        </tr>
        <tr>
            <td>Tiền nước</td>
            <td>{{ number_format(($hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu) * $hoaDon->don_gia_nuoc) }} VND</td>
        </tr>
        <tr style="background-color: #ffeeba;">
            <td><strong>Thành tiền</strong></td>
            <td><strong>{{ number_format($hoaDon->thanh_tien) }} VND</strong></td>
        </tr>
    </tbody>
</table>

<p style="margin-top: 20px;">Vui lòng thanh toán trước ngày <strong>10 hàng tháng</strong>.</p>
<p>Trân trọng!</p>
