@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>💥 Chi tiết sự cố #{{ $suco->id }}</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

        {{-- 🖼️ Ảnh minh chứng --}}
        <div class="text-center mb-4">
            @if(!empty($suco->anh) && file_exists(public_path($suco->anh)))
                <img src="{{ asset($suco->anh) }}" 
                     alt="Ảnh sự cố" 
                     class="img-thumbnail shadow-sm" 
                     width="320" 
                     style="border-radius: 10px; object-fit: cover;">
            @else
                <img src="{{ asset('images/no-image.png') }}" 
                     alt="Không có ảnh" 
                     class="img-thumbnail" 
                     width="320" 
                     style="opacity: 0.6;">
                <p class="text-muted mt-2">Chưa có ảnh minh chứng</p>
            @endif
        </div>

        {{-- 🧾 Thông tin chi tiết --}}
        <table class="table table-bordered">
            <tr>
                <th width="25%">👨‍🎓 Sinh viên</th>
                <td>
                    @if($suco->sinhVien)
                        <strong>{{ $suco->sinhVien->ho_ten }}</strong><br>
                        <small>MSSV: {{ $suco->sinhVien->ma_sinh_vien ?? '---' }}</small><br>
                        <small>Email: {{ $suco->sinhVien->email ?? '---' }}</small>
                    @else
                        <em>Không xác định</em>
                    @endif
                </td>
            </tr>
            <tr>
                <th>🏠 Phòng</th>
                <td>{{ $suco->phong->ten_phong ?? '---' }}</td>
            </tr>
            <tr>
                <th>📝 Mô tả sự cố</th>
                <td>{{ $suco->mo_ta ?? '---' }}</td>
            </tr>
            <tr>
                <th>📅 Ngày gửi</th>
                <td>{{ \Carbon\Carbon::parse($suco->ngay_gui)->format('d/m/Y') }}</td>
            </tr>

            {{-- ✅ Ngày hoàn thành --}}
            <tr>
                <th>📆 Ngày hoàn thành</th>
                <td>
                    @if($suco->ngay_hoan_thanh)
                        {{ \Carbon\Carbon::parse($suco->ngay_hoan_thanh)->format('d/m/Y') }}
                    @else
                        <em>Chưa hoàn thành</em>
                    @endif
                </td>
            </tr>

            <tr>
                <th>⚙️ Trạng thái xử lý</th>
                <td>
                    <span class="badge 
                        @if($suco->trang_thai == 'Tiếp nhận') bg-secondary
                        @elseif($suco->trang_thai == 'Đang xử lý') bg-info
                        @elseif($suco->trang_thai == 'Hoàn thành') bg-success
                        @else bg-danger
                        @endif">
                        {{ $suco->trang_thai }}
                    </span>
                </td>
            </tr>

            {{-- 💰 Thông tin thanh toán --}}
            <tr>
                <th>💸 Số tiền cần thanh toán</th>
                <td>
                    @if($suco->payment_amount > 0)
                        <strong class="text-danger">{{ number_format($suco->payment_amount, 0, ',', '.') }} VNĐ</strong>
                    @else
                        <em>Không yêu cầu thanh toán</em>
                    @endif
                </td>
            </tr>
            <tr>
                <th>💵 Trạng thái thanh toán</th>
                <td>
                    @if($suco->payment_amount > 0)
                        @if($suco->is_paid)
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Không cần thanh toán</span>
                    @endif

                    @if($suco->payment_amount > 0 && !$suco->is_paid && (Auth::user()->role === 'admin' || Auth::user()->role === 'nhanvien'))
                        <form action="{{ route('suco.thanhtoan', $suco->id) }}" 
                              method="POST" 
                              class="d-inline"
                              onsubmit="return confirm('Xác nhận sinh viên đã thanh toán sự cố này?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success ms-2">
                                <i class="fa fa-check"></i> Xác nhận đã thanh toán
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        </table>

        {{-- 💰 Thông tin hóa đơn sự cố --}}
        <div class="x_panel mt-4">
            <div class="x_title">
                <h2>🧾 Thông tin hóa đơn sự cố</h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                @if(isset($hoaDon) && $hoaDon)
                    <p><strong>Số tiền:</strong> {{ number_format($hoaDon->amount, 0, ',', '.') }} VNĐ</p>
                    <p><strong>Trạng thái:</strong> 
                        @if($hoaDon->status === 'Đã thanh toán')
                            <span class="badge bg-success">Đã thanh toán</span>
                        @else
                            <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                        @endif
                    </p>
                    <p><strong>Ngày tạo:</strong> {{ \Carbon\Carbon::parse($hoaDon->ngay_tao)->format('d/m/Y') }}</p>
                    <p><strong>Ngày thanh toán:</strong> {{ $hoaDon->ngay_thanh_toan ? \Carbon\Carbon::parse($hoaDon->ngay_thanh_toan)->format('d/m/Y') : 'Chưa có' }}</p>
                @else
                    <p>⚠️ Chưa có hóa đơn cho sự cố này.</p>
                    <a href="{{ route('suco.formTaoHoaDon', $suco->id) }}" class="btn btn-primary">
                        ➕ Tạo hóa đơn
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('suco.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Quay lại
            </a>
            <a href="{{ route('suco.edit', $suco->id) }}" class="btn btn-warning">
                <i class="fa fa-edit"></i> Cập nhật
            </a>
        </div>
    </div>
</div>

<style>
.table th { background-color: #f8f9fa; }
.badge {
    padding: 6px 10px;
    border-radius: 12px;
    color: #fff;
    font-size: 12px;
}
.bg-secondary { background-color: #6c757d !important; }
.bg-info { background-color: #17a2b8 !important; }
.bg-success { background-color: #28a745 !important; }
.bg-danger { background-color: #dc3545 !important; }
.bg-warning { background-color: #ffc107 !important; }
</style>
@endsection
