<p>Chào {{ $sinhVien->ho_ten }},</p>

<p>Cảm ơn bạn đã đăng ký ký túc xá. Hồ sơ của bạn đã được gửi thành công và đang chờ ban quản lý xét duyệt.</p>

<p><strong>Thông tin đăng ký:</strong></p>
<ul>
    <li>Mã sinh viên: {{ $sinhVien->ma_sinh_vien }}</li>
    <li>Họ tên: {{ $sinhVien->ho_ten }}</li>
    <li>Lớp: {{ $sinhVien->lop }}</li>
    <li>Ngành: {{ $sinhVien->nganh }}</li>
    <li>Trạng thái: {{ $sinhVien->trang_thai_ho_so }}</li>
</ul>

<p>Vui lòng đăng nhập vào cổng sinh viên để theo dõi tình trạng hồ sơ của bạn.</p>

<p>Cảm ơn bạn đã lựa chọn ký túc xá của chúng tôi!</p>

<p>Trân trọng,<br>Ban quản lý ký túc xá</p>

