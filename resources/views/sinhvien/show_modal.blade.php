@if (isset($sinhvien->anh_sinh_vien))
    <div class="w-100 d-flex align-items-center justify-content-center">
        <img src="{{ asset('storage/' . $sinhvien->anh_sinh_vien) }}" alt="{{ $sinhvien->ho_ten }}" width="200px"
            height="200px">
    </div>
@endif

<table class="table table-bordered">
    <colgroup>
        <col width="30%">
        <col width="70%">
    </colgroup>
    <tbody>
        <tr>
            <th scope="row">Mã sinh viên</th>
            <td>{{ $sinhvien->ma_sinh_vien }}</td>
        </tr>
        <tr>
            <th scope="row">Họ và tên</th>
            <td>{{ $sinhvien->ho_ten }}</td>
        </tr>
        <tr>
            <th scope="row">Ngày sinh</th>
            <td>
                {{ !empty($sinhvien->ngay_sinh) ? \Carbon\Carbon::parse($sinhvien->ngay_sinh)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        <tr>
            <th scope="row">Giới tính</th>
            <td>{{ $sinhvien->gioi_tinh ?? '-' }}</td>
        </tr>

          <tr>
            <th scope="row">Người thân</th>
            <td>
                {{ $sinhvien->guardian_name ? $sinhvien->guardian_name . ' (' . $sinhvien->guardian_relationship . ')' : '-' }}
                @if (!empty($sinhvien->guardian_phone))
                    , {{ $sinhvien->guardian_phone }}
                @endif
            </td>
        </tr>

        <tr>
            <th scope="row">Số CCCD</th>
            <td>{{ $sinhvien->citizen_id_number ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Ngày cấp CCCD</th>
            <td>{{ $sinhvien->citizen_issue_date ? \Carbon\Carbon::parse($sinhvien->citizen_issue_date)->format('d/m/Y') : '-' }}
            </td>
        </tr>
        <tr>
            <th scope="row">Nơi cấp CCCD</th>
            <td>{{ $sinhvien->citizen_issue_place ?? '-' }}</td>
        </tr>

        <tr>
            <th scope="row">Lớp</th>
            <td>{{ $sinhvien->lop ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Ngành</th>
            <td>{{ $sinhvien->nganh ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Khóa học</th>
            <td>{{ $sinhvien->khoa_hoc ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Quê quán</th>
            <td> {{ $sinhvien->que_quan }}</td>
        </tr>
        <tr>
            <th scope="row">Nơi ở hiện tại</th>
            <td>{{ $sinhvien->noi_o_hien_tai }}</td>
        </tr>
        <tr>
            <th scope="row">Số điện thoại</th>
            <td>{{ $sinhvien->so_dien_thoai ?? '-' }}</td>
        </tr>
        <tr>
            <th scope="row">Email</th>
            <td>{{ $sinhvien->email }}</td>
        </tr>
        <tr>
            <th scope="row">Trạng thái hồ sơ</th>
            <td> @php
                $status = $sinhvien->trang_thai_ho_so ?? 'Khác';
                $badge = match ($status) {
                    'Đã duyệt' => 'bg-success',
                    'Chờ duyệt' => 'bg-warning',
                    default => 'bg-secondary',
                };
            @endphp
                <span class="badge {{ $badge }}">{{ $status }}</span>
            </td>
        </tr>
    </tbody>
</table>
