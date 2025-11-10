    @extends('admin.layouts.admin')

    @section('content')
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-edit text-warning"></i> Chỉnh sửa sự cố #{{ $suco->id }}</h2>
            <div class="clearfix"></div>
        </div>

        <div class="x_content">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('suco.update', $suco->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Sinh viên</label>
                    <input type="text" class="form-control" 
                        value="{{ $suco->sinhVien->ho_ten }} ({{ $suco->sinhVien->ma_sinh_vien }})" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phòng / Khu</label>
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
                        $tenPhongDisplay = $phong && isset($phong->ten_phong) ? $phong->ten_phong : 'Chưa có phòng';
                        $khu = ($phong && isset($phong->khu) && $phong->khu) ? $phong->khu : null;
                        $tenKhuDisplay = $khu && isset($khu->ten_khu) ? $khu->ten_khu : null;
                    @endphp
                    <input type="text" class="form-control" 
                        value="{{ $tenPhongDisplay }}@if($tenKhuDisplay) - Khu {{ $tenKhuDisplay }}@endif" disabled>
                </div>

                <div class="mb-3">
                    <label for="mo_ta" class="form-label">Mô tả sự cố</label>
                    <textarea name="mo_ta" class="form-control" rows="4" required>{{ old('mo_ta', $suco->mo_ta) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="trang_thai" class="form-label">Trạng thái xử lý</label>
                    <select name="trang_thai" id="trang_thai" class="form-control" required>
                        <option value="Tiếp nhận" {{ $suco->trang_thai == 'Tiếp nhận' ? 'selected' : '' }}>Tiếp nhận</option>
                        <option value="Đang xử lý" {{ $suco->trang_thai == 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="Hoàn thành" {{ $suco->trang_thai == 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                    </select>
                </div>

                {{-- 🗓️ Ngày hoàn thành (chỉ hiển thị khi trạng thái = Hoàn thành) --}}
                <div class="mb-3" id="hoan_thanh_field" 
                    style="display: {{ $suco->trang_thai == 'Hoàn thành' ? 'block' : 'none' }}">
                    <label for="ngay_hoan_thanh" class="form-label">Ngày hoàn thành</label>
                    <input type="date" name="ngay_hoan_thanh" class="form-control"
                        value="{{ old('ngay_hoan_thanh', $suco->ngay_hoan_thanh ? \Carbon\Carbon::parse($suco->ngay_hoan_thanh)->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-3">
                    <label for="payment_amount" class="form-label">Giá tiền (₫)</label>
                    <input type="number" name="payment_amount" class="form-control" 
                        value="{{ old('payment_amount', $suco->payment_amount) }}">
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="is_paid" class="form-check-input" id="is_paid"
                        value="1" {{ $suco->is_paid ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_paid">Đã thanh toán</label>
                </div>

                <div class="mb-3">
                    <label for="anh" class="form-label">Ảnh minh chứng</label>
                    @if($suco->anh)
                        <div class="mb-2">
                            <img src="{{ asset($suco->anh) }}" alt="Ảnh sự cố" 
                                style="width:120px; height:80px; object-fit:cover; border-radius:6px;">
                        </div>
                    @endif
                    <input type="file" name="anh" class="form-control">
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fa fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('suco.index') }}" class="btn btn-light">Hủy</a>
            </form>
        </div>
    </div>

    {{-- ✅ Script: Tự động ẩn/hiện ngày hoàn thành --}}
    <script>
    document.getElementById('trang_thai').addEventListener('change', function() {
        const field = document.getElementById('hoan_thanh_field');
        field.style.display = (this.value === 'Hoàn thành') ? 'block' : 'none';
    });
    </script>
    @endsection
