<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo phân phòng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .info-box {
            background-color: white;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .info-row {
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 150px;
        }
        .value {
            color: #111827;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thông báo phân phòng</h1>
    </div>
    
    <div class="content">
        <h2>Xin chào {{ $sinhVien->ho_ten }}!</h2>
        
        <p>Bạn đã được phân phòng thành công. Thông tin chi tiết như sau:</p>
        
        <div class="info-box">
            <div class="info-row">
                <span class="label">Mã sinh viên:</span>
                <span class="value">{{ $sinhVien->ma_sinh_vien }}</span>
            </div>
            <div class="info-row">
                <span class="label">Khu:</span>
                <span class="value">{{ optional($phong->khu)->ten_khu ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Phòng:</span>
                <span class="value">{{ $phong->ten_phong }}</span>
            </div>
            <div class="info-row">
                <span class="label">Mã slot:</span>
                <span class="value">{{ $slot->ma_slot }}</span>
            </div>
            <div class="info-row">
                <span class="label">Loại phòng:</span>
                <span class="value">{{ $phong->loai_phong ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="label">Ngày phân phòng:</span>
                <span class="value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
            </div>
        </div>
        
        <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; font-weight: bold; color: #856404; font-size: 16px;">
                💰 Số tiền cần thanh toán: <strong style="color: #059669; font-size: 18px;">{{ number_format($phong->giaSlot() ?? 0, 0, ',', '.') }} đ/slot</strong>
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
            <a href="{{ route('client.room.confirmation.show') }}" style="display: inline-block; padding: 18px 40px; background-color: #2563eb; color: #ffffff !important; text-decoration: none; border-radius: 10px; font-weight: bold; font-size: 18px; box-shadow: 0 4px 8px rgba(37, 99, 235, 0.4);">
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
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">Nếu nút trên không hoạt động, hãy đăng nhập vào hệ thống và vào trang "Tổng quan" để xem thông tin chi tiết và thanh toán.</p>
        
        <p style="margin-top: 20px;">
            <strong>Lưu ý:</strong> Vui lòng thanh toán tiền phòng để hoàn tất thủ tục vào phòng. Chưa thanh toán = Chưa được vào phòng.
        </p>
        
        <p style="margin-top: 30px;">
            Trân trọng,<br>
            <strong>Ban quản lý Ký túc xá</strong>
        </p>
    </div>
    
    <div class="footer">
        <p>Email này được gửi tự động từ hệ thống quản lý ký túc xá.</p>
        <p>Vui lòng không trả lời email này.</p>
    </div>
</body>
</html>

