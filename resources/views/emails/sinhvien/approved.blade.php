<p>Chào {{ $sinhVien->ho_ten }},</p>

<p>Ban quản lý ký túc xá đã duyệt hồ sơ đăng ký của bạn. Vui lòng đăng nhập vào cổng sinh viên và xác nhận thông tin để hoàn tất thủ tục.</p>

<p>
    <a href="{{ $confirmationUrl }}" style="display:inline-block;padding:10px 18px;background-color:#2563eb;color:#ffffff;text-decoration:none;border-radius:6px;">
        Xác nhận hồ sơ
    </a>
</p>

<p>Nếu nút trên không hoạt động, hãy sao chép đường dẫn sau và mở trong trình duyệt:</p>
<p>{{ $confirmationUrl }}</p>

<p>Cảm ơn bạn đã lựa chọn ký túc xá của chúng tôi!</p>

