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

        {{-- 🖼️ Ảnh sau khi xử lý (nếu có) --}}
        @if(!empty($suco->anh_sau) && file_exists(public_path($suco->anh_sau)))
        <div class="text-center mb-4">
            <div class="mb-2 text-muted">Ảnh sau khi xử lý</div>
            <img src="{{ asset($suco->anh_sau) }}" 
                 alt="Ảnh sau xử lý" 
                 class="img-thumbnail shadow-sm" 
                 width="320" 
                 style="border-radius: 10px; object-fit: cover;">
        </div>
        @endif

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
                <th>🏠 Phòng / Khu</th>
                <td>
                    @php
                        // Ưu tiên lấy phòng từ slot (nếu có), nếu không thì lấy từ phong_id trực tiếp
                        $student = $suco->sinhVien ?? null;
                        $phong = null;
                        if ($student) {
                            // Kiểm tra slot và phong của slot
                            if (isset($student->slot) && $student->slot && isset($student->slot->phong) && $student->slot->phong) {
                                $phong = $student->slot->phong;
                            } elseif (isset($student->phong) && $student->phong) {
                                $phong = $student->phong;
                            } elseif (isset($suco->phong) && $suco->phong) {
                                $phong = $suco->phong;
                            }
                        } elseif (isset($suco->phong) && $suco->phong) {
                            $phong = $suco->phong;
                        }
                        $tenPhongDisplay = $phong && isset($phong->ten_phong) ? $phong->ten_phong : null;
                        $khu = ($phong && isset($phong->khu) && $phong->khu) ? $phong->khu : null;
                        $tenKhuDisplay = $khu && isset($khu->ten_khu) ? $khu->ten_khu : null;
                    @endphp
                    @if ($tenPhongDisplay)
                        <strong>{{ $tenPhongDisplay }}</strong>
                        @if ($tenKhuDisplay)
                            <span class="badge badge-soft-secondary ml-2">Khu {{ $tenKhuDisplay }}</span>
                        @endif
                    @else
                        <em>Chưa được phân phòng</em>
                    @endif
                </td>
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
            {{-- 📈 Độ hoàn thiện --}}
            <tr>
                <th>📈 Độ hoàn thiện</th>
                <td>
                    @if(isset($suco->completion_percent))
                        {{ $suco->completion_percent }}%
                    @else
                        <em>Chưa cập nhật</em>
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
                        <em>Chưa có hóa đơn</em>
                        {{-- Nút tạo hóa đơn (chỉ hiện khi chưa có payment_amount và là admin/nhanvien) --}}
                        @if((Auth::user()->role === 'admin' || Auth::user()->role === 'nhanvien'))
                            <button type="button" class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#taoHoaDonModal">
                                <i class="fa fa-file-invoice"></i> Tạo hóa đơn
                            </button>
                        @endif
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

                        {{-- Nút xác nhận thanh toán (chỉ hiện khi chưa thanh toán và là admin/nhanvien) --}}
                        @if(!$suco->is_paid && (Auth::user()->role === 'admin' || Auth::user()->role === 'nhanvien'))
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
                    @else
                        <span class="badge bg-secondary">Chưa có hóa đơn</span>
                    @endif
                </td>
            </tr>
            {{-- ⭐ Đánh giá (chỉ sau khi đã thanh toán) --}}
            <tr id="rating">
                <th>⭐ Đánh giá xử lý</th>
                <td>
                    @if($suco->is_paid)
                        @if($suco->rating)
                            <div class="mb-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $suco->rating ? 'text-warning' : 'text-muted' }}">★</span>
                                @endfor
                                <span class="ms-2 text-muted">({{ $suco->rating }}/5)</span>
                            </div>
                            @if($suco->feedback)
                                <div class="text-muted">"{{ $suco->feedback }}"</div>
                            @endif
                        @else
                            <form action="{{ route('suco.danhgia', $suco->id) }}" method="POST" class="d-inline">
                                @csrf
                                <div class="d-flex align-items-center gap-2">
                                    <select name="rating" class="form-select form-select-sm" style="width:auto;">
                                        <option value="5">5 sao</option>
                                        <option value="4">4 sao</option>
                                        <option value="3">3 sao</option>
                                        <option value="2">2 sao</option>
                                        <option value="1">1 sao</option>
                                    </select>
                                    <input type="text" name="feedback" class="form-control form-control-sm" placeholder="Góp ý (tùy chọn)" style="max-width:320px;">
                                    <button type="submit" class="btn btn-sm btn-primary">Gửi đánh giá</button>
                                </div>
                            </form>
                        @endif
                    @else
                        <em>Chỉ có thể đánh giá sau khi đã thanh toán.</em>
                    @endif
                </td>
            </tr>
        </table>

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

{{-- Modal tạo hóa đơn --}}
@if((Auth::user()->role === 'admin' || Auth::user()->role === 'nhanvien') && $suco->payment_amount == 0)
<div class="modal fade" id="taoHoaDonModal" tabindex="-1" aria-labelledby="taoHoaDonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taoHoaDonModalLabel">💰 Tạo hóa đơn sự cố</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('suco.taohoadon', $suco->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Số tiền (VNĐ)</label>
                        <input type="number" 
                               class="form-control" 
                               id="payment_amount" 
                               name="payment_amount" 
                               min="0" 
                               step="1000" 
                               required
                               placeholder="Nhập số tiền (0 = không cần thanh toán)">
                        <small class="form-text text-muted">
                            <strong>Lưu ý:</strong><br>
                            • Nhập <strong>0</strong> nếu sự cố do ký túc xá (không cần thanh toán)<br>
                            • Nhập số tiền > 0 nếu sự cố do sinh viên gây ra (cần thanh toán)
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Tạo hóa đơn
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
