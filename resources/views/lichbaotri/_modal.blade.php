<div>
  <h5 class="mb-3 fw-bold text-primary">
    🧰 Thông tin bảo trì
  </h5>

  <table class="table table-bordered align-middle">
    {{-- 🔹 Tên & mã tài sản --}}
    <tr>
      <th style="width:180px;">Tài sản</th>
      <td>
        @php
          $tenTaiSan = $lich->taiSan->ten_tai_san ?? $lich->khoTaiSan->ten_tai_san ?? 'Không xác định';
          $maTaiSan = $lich->taiSan->khoTaiSan->ma_tai_san
                      ?? $lich->khoTaiSan->ma_tai_san
                      ?? null;
        @endphp
        <strong>{{ $tenTaiSan }}</strong>
        <small class="text-muted">({{ $maTaiSan ?? 'Không có mã' }})</small>
      </td>
    </tr>

    {{-- 🔹 Vị trí --}}
    <tr>
      <th>Vị trí</th>
      <td>
        @if($lich->taiSan && $lich->taiSan->phong)
          Phòng: <strong>{{ $lich->taiSan->phong->ten_phong }}</strong>
        @elseif($lich->khoTaiSan)
          <span class="text-secondary">Kho</span>
        @else
          -
        @endif
      </td>
    </tr>

    {{-- 🔹 Người sử dụng --}}
    <tr>
      <th>Sinh viên sử dụng</th>
      <td>
        @php
          $slot = $lich->taiSan?->slots?->first();
          $sinhVien = $slot?->sinhVien;
        @endphp

        @if($sinhVien)
          {{ $sinhVien->ho_ten }}
          <small class="text-muted">({{ $sinhVien->ma_sinh_vien }})</small>
        @else
          <span class="text-muted">Tài sản chung</span>
        @endif
      </td>
    </tr>

    {{-- 🔹 Slot --}}
    <tr>
      <th>Mã Slot</th>
      <td>{{ $slot?->ma_slot ?? '-' }}</td>
    </tr>

    {{-- 🔹 Ngày --}}
    <tr>
      <th>Ngày bảo trì</th>
      <td>{{ \Carbon\Carbon::parse($lich->ngay_bao_tri)->format('d/m/Y') }}</td>
    </tr>
    <tr>
      <th>Ngày hoàn thành</th>
      <td>{{ $lich->ngay_hoan_thanh ? \Carbon\Carbon::parse($lich->ngay_hoan_thanh)->format('d/m/Y') : '-' }}</td>
    </tr>

    {{-- 🔹 Mô tả trước/sau --}}
    <tr>
      <th>Mô tả bảo trì</th>
      <td>
        @if($lich->mo_ta)
          <div class="mb-2 p-2 border rounded bg-light">
            <strong>🔧 Trước bảo trì:</strong>
            <div class="ms-3">{{ $lich->mo_ta }}</div>
          </div>
        @endif

        @if($lich->mo_ta_sau)
          <div class="p-2 border rounded bg-light">
            <strong>✅ Sau bảo trì:</strong>
            <div class="ms-3">{{ $lich->mo_ta_sau }}</div>
          </div>
        @endif

        @if(!$lich->mo_ta && !$lich->mo_ta_sau)
          <em>Không có mô tả</em>
        @endif
      </td>
    </tr>

    {{-- 🔹 Trạng thái --}}
    <tr>
      <th>Trạng thái</th>
      <td>
        <span class="badge
          @if($lich->trang_thai == 'Hoàn thành') bg-success text-white
          @elseif($lich->trang_thai == 'Đang bảo trì') bg-warning text-dark
          @elseif($lich->trang_thai == 'Đang lên lịch') bg-info text-white
          @elseif($lich->trang_thai == 'Chờ bảo trì') bg-primary text-white
          @elseif($lich->trang_thai == 'Chờ thanh toán') bg-danger text-white
          @elseif($lich->trang_thai == 'Đã thanh toán') bg-success text-white
          @else bg-secondary text-white @endif">
          {{ $lich->trang_thai ?? 'Chưa xác định' }}
        </span>
      </td>
    </tr>
  </table>

  {{-- 🔹 Hình ảnh trước/sau --}}
  <div class="row mt-4">
    <div class="col-md-6 text-center">
      <h6 class="fw-semibold text-secondary mb-2">Ảnh trước bảo trì</h6>
      @if($lich->hinh_anh_truoc && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh_truoc)))
        <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh_truoc) }}"
             class="img-fluid rounded shadow-sm border"
             style="max-height:250px;object-fit:cover;">
      @else
        <div class="text-muted small">Không có ảnh</div>
      @endif
    </div>

    <div class="col-md-6 text-center">
      <h6 class="fw-semibold text-secondary mb-2">Ảnh sau bảo trì</h6>
      @if($lich->hinh_anh && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh)))
        <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh) }}"
             class="img-fluid rounded shadow-sm border"
             style="max-height:250px;object-fit:cover;">
      @else
        <div class="text-muted small">Chưa cập nhật</div>
      @endif
    </div>
  </div>
</div>
