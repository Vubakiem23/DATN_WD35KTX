<div>
  <h5 class="mb-3">Thông tin bảo trì</h5>
  <table class="table table-bordered">
    <tr>
      <th>Tài sản</th>
      <td>
        @php
        $tenTaiSan = $lich->taiSan->ten_tai_san ?? $lich->khoTaiSan->ten_tai_san ?? 'Không xác định';
        $maTaiSan = $lich->taiSan->khoTaiSan->ma_tai_san
        ?? $lich->khoTaiSan->ma_tai_san
        ?? null;
        @endphp

        {{ $tenTaiSan }}
        <small class="text-muted">({{ $maTaiSan ?? 'Không có mã' }})</small>
      </td>
    </tr>
    <tr>
      <th>Vị trí</th>
      <td>
        @if($lich->taiSan && $lich->taiSan->phong)
        Phòng: {{ $lich->taiSan->phong->ten_phong }}
        @elseif($lich->khoTaiSan)
        Kho
        @else
        -
        @endif
      </td>
    </tr>
    <tr>
      <th>Ngày bảo trì</th>
      <td>{{ \Carbon\Carbon::parse($lich->ngay_bao_tri)->format('d/m/Y') }}</td>
    </tr>
    <tr>
      <th>Ngày hoàn thành</th>
      <td>{{ $lich->ngay_hoan_thanh ? \Carbon\Carbon::parse($lich->ngay_hoan_thanh)->format('d/m/Y') : '-' }}</td>
    </tr>
    <tr>
      <th>Mô tả</th>
      <td>{{ $lich->mo_ta ?? '-' }}</td>
    </tr>
    <tr>
      <th>Trạng thái</th>
      <td>
        <span class="badge 
          @if($lich->trang_thai == 'Hoàn thành') bg-success
          @elseif($lich->trang_thai == 'Đang bảo trì') bg-warning text-dark
          @else bg-secondary @endif">
          {{ $lich->trang_thai }}
        </span>
      </td>
    </tr>
  </table>

  <div class="row mt-4">
    <div class="col-md-6 text-center">
      <h6>Ảnh trước bảo trì</h6>
      @if($lich->hinh_anh_truoc && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh_truoc)))
      <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh_truoc) }}" class="img-fluid rounded shadow-sm" style="max-height:250px;">
      @else
      <div class="text-muted small">Không có ảnh</div>
      @endif
    </div>
    <div class="col-md-6 text-center">
      <h6>Ảnh sau bảo trì</h6>
      @if($lich->hinh_anh && file_exists(public_path('uploads/lichbaotri/'.$lich->hinh_anh)))
      <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh) }}"
        class="img-fluid rounded shadow-sm"
        style="max-height:250px;">
      @else
      <div class="text-muted small">Chưa cập nhật</div>
      @endif
    </div>

  </div>
</div>