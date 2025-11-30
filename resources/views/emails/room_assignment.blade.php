<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo gán phòng ký túc xá</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
        <h2 style="color: #2563eb; margin-top: 0;">Thông báo gán phòng ký túc xá</h2>
        
        <p>Chào <strong>{{ $sinhVien->ho_ten }}</strong>,</p>
        
        <p>Ban quản lý ký túc xá đã gán bạn vào phòng <strong>{{ $assignment->phong->ten_phong ?? 'N/A' }}</strong>@if($assignment->phong && $assignment->phong->khu) - Khu {{ $assignment->phong->khu->ten_khu }}@endif.</p>
        
        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #856404; font-size: 16px;">
                💰 Số tiền cần thanh toán: <strong style="color: #059669; font-size: 18px;">{{ number_format($assignment->phong->giaSlot() ?? 0, 0, ',', '.') }} đ/slot</strong>
            </p>
            <p style="margin: 10px 0 0 0; color: #856404; font-size: 14px;">
                Đây là tiền phòng tháng đầu tiên. Bạn cần thanh toán để hoàn tất thủ tục vào phòng.
            </p>
        </div>
        
        <p style="font-size: 16px; font-weight: bold; color: #dc3545; margin: 20px 0;">
            ⚠️ LƯU Ý: Bạn cần thanh toán tiền phòng để được vào phòng. Chưa thanh toán = Chưa được vào phòng.
        </p>
        
        <p><strong>Vui lòng click vào nút bên dưới để thanh toán và xác nhận vào phòng:</strong></p>
        
        <div style="margin: 30px 0; text-align: center;">
            <a href="{{ $confirmationUrl }}" style="display: inline-block; padding: 18px 40px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 18px; box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4); transition: all 0.3s;">
                💳 THANH TOÁN VÀ XÁC NHẬN VÀO PHÒNG
            </a>
        </div>
        
        <div style="background-color: #e7f3ff; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #004085; font-size: 14px;">
                <strong>Hướng dẫn:</strong><br>
                1. Click nút "THANH TOÁN VÀ XÁC NHẬN VÀO PHÒNG" ở trên<br>
                2. Chọn hình thức thanh toán (Tiền mặt hoặc Chuyển khoản)<br>
                3. Hoàn tất thanh toán để được vào phòng
            </p>
        </div>
        
        <div style="margin: 20px 0; text-align: center;">
            <p style="margin: 0; color: #666; font-size: 14px;">
                Hoặc bạn có thể từ chối phòng này nếu không muốn ở.
            </p>
        </div>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">Nếu nút trên không hoạt động, hãy sao chép đường dẫn sau và mở trong trình duyệt:</p>
        <p style="color: #2563eb; font-size: 14px; word-break: break-all; background-color: #f0f0f0; padding: 10px; border-radius: 4px;">{{ $confirmationUrl }}</p>
        
        <p style="margin-top: 30px; color: #666; font-size: 14px;">Cảm ơn bạn!</p>
    </div>
</body>
</html>


