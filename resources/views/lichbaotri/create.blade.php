@extends('admin.layouts.admin')

@section('title', 'Lên lịch bảo trì')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">🛠️ Lên lịch bảo trì</h4>

  {{-- Hiển thị lỗi --}}
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('lichbaotri.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- Nếu được mở từ trang "Lên lịch bảo trì" của tài sản --}}
    @if(isset($taiSan))
      <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-center gap-3">
          <div>
            <img src="{{ asset('storage/' . ($taiSan->khoTaiSan->hinh_anh ?? '')) }}" 
                 alt="Ảnh tài sản" 
                 style="width:150px;height:150px;object-fit:cover;border-radius:10px;">
          </div>
          <div>
            <h5 class="mb-1">{{ $taiSan->khoTaiSan->ten_tai_san ?? 'Không rõ tên' }}</h5>
            <p class="mb-1"><strong>Mã tài sản:</strong> {{ $taiSan->khoTaiSan->ma_tai_san ?? '—' }}</p>
            <p class="mb-1"><strong>Phòng:</strong> {{ $taiSan->phong->ten_phong ?? '—' }}</p>
            <p class="mb-1"><strong>Tình trạng hiện tại:</strong> {{ $taiSan->tinh_trang_hien_tai ?? '—' }}</p>
            @php
              $slot = optional($taiSan->slots)->first();
              $sv = $slot?->sinhVien;
            @endphp
            <p class="mb-1"><strong>Mã slot:</strong> {{ $slot->ma_slot ?? '—' }}</p>
            <p class="mb-1"><strong>Sinh viên đang sử dụng:</strong> {{ $sv->ho_ten ?? 'Tài sản chung' }}</p>
            @if(!empty($sv?->ma_sinh_vien))
              <p class="mb-0"><strong>Mã sinh viên:</strong> {{ $sv->ma_sinh_vien }}</p>
            @endif
          </div>
        </div>
      </div>

      <input type="hidden" name="tai_san_id" value="{{ $taiSan->id }}">
    @else
      <div class="mb-3">
        <label class="form-label">Chọn vị trí</label>
        <select id="vi_tri" class="form-select form-control" required>
          <option value="">-- Chọn vị trí --</option>
          <option value="phong">Tài sản trong phòng</option>
          <option value="kho">Tài sản trong kho</option>
        </select>
      </div>

      {{-- 🔹 Nếu chọn "Kho" --}}
      <div class="vi-tri-kho d-none">
        <div class="mb-3">
          <label class="form-label">Chọn loại tài sản (trong kho)</label>
          <select id="loai_tai_san_kho" class="form-select form-control">
            <option value="">-- Chọn loại tài sản --</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Chọn tài sản trong kho</label>
          <select name="tai_san_id" id="tai_san_kho" class="form-select form-control">
            <option value="">-- Chọn tài sản --</option>
          </select>
        </div>
      </div>

      {{-- 🔹 Nếu chọn "Phòng" --}}
      <div class="vi-tri-phong d-none">
        <div class="mb-3">
          <label class="form-label">Chọn phòng</label>
          <select id="phong_id" class="form-select form-control">
            <option value="">-- Chọn phòng --</option>
            @foreach ($phongs as $phong)
              <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Chọn tài sản trong phòng</label>
          <select name="tai_san_id" id="tai_san_phong" class="form-select form-control">
            <option value="">-- Chọn tài sản --</option>
          </select>

          <div id="preview_taisan" class="mt-3 text-center"></div>
        </div>
      </div>
    @endif

    <div class="mb-3">
      <label class="form-label">Ngày bảo trì</label>
      <input type="date" name="ngay_bao_tri" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Mô tả</label>
      <textarea name="mo_ta" class="form-control" rows="3" placeholder="Nhập mô tả (nếu có)"></textarea>
    </div>

    <div class="mb-3">
      <label class="form-label">Ảnh minh chứng (nếu có)</label>
      <input type="file" name="hinh_anh_truoc" class="form-control" accept="image/*">
    </div>

    <button type="submit" class="btn btn-primary">💾 Lưu lịch bảo trì</button>
    <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Quay lại</a>
  </form>
</div>

@if(!isset($taiSan))
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function() {
    const viTriSelect = $('#vi_tri');
    const khoSection = $('.vi-tri-kho');
    const phongSection = $('.vi-tri-phong');
    const loaiSelect = $('#loai_tai_san_kho');
    const taiSanKhoSelect = $('#tai_san_kho');
    const phongSelect = $('#phong_id');
    const taiSanPhongSelect = $('#tai_san_phong');
    const previewTaiSan = $('#preview_taisan');

    viTriSelect.on('change', function() {
      const viTri = $(this).val();
      khoSection.addClass('d-none');
      phongSection.addClass('d-none');
      previewTaiSan.html('');

      if (viTri === 'kho') {
        khoSection.removeClass('d-none');
        loadLoaiTaiSanKho();
      } else if (viTri === 'phong') {
        phongSection.removeClass('d-none');
      }
    });

    loaiSelect.on('change', function() {
      const loaiId = $(this).val();
      if (!loaiId) return;
      loadTaiSanKho(loaiId);
    });

    phongSelect.on('change', function() {
      const phongId = $(this).val();
      if (!phongId) return;
      loadTaiSanPhong(phongId);
    });

    function loadTaiSanKho(loaiId) {
      taiSanKhoSelect.html('<option>-- Đang tải tài sản... --</option>');
      $.get(`/admin/lichbaotri/get-tai-san-kho/${loaiId}`, function(data) {
        taiSanKhoSelect.html('<option value="">-- Chọn tài sản --</option>');
        data.forEach(item => {
          taiSanKhoSelect.append(`
            <option value="${item.id}" data-img="${item.hinh_anh}" data-ten="${item.ten_tai_san}">
              [${item.ma_tai_san}] ${item.ten_tai_san}
            </option>
          `);
        });
      });
    }

    function loadTaiSanPhong(phongId) {
      taiSanPhongSelect.html('<option>-- Đang tải tài sản... --</option>');
      $.get(`/admin/lichbaotri/get-tai-san-phong/${phongId}`, function(data) {
        taiSanPhongSelect.html('<option value="">-- Chọn tài sản --</option>');
        data.forEach(item => {
          taiSanPhongSelect.append(`
            <option
              value="${item.id}"
              data-img="${item.hinh_anh ?? ''}"
              data-ten="${item.ten_tai_san}"
              data-nguoi="${item.nguoi_su_dung}"
              data-masv="${item.ma_sinh_vien ?? ''}"
              data-slot="${item.ma_slot ?? ''}"
            >
              [${item.ma_tai_san}] ${item.ten_tai_san}
              - Sử dụng: ${item.nguoi_su_dung}
              ${item.ma_sinh_vien ? ' - Mã SV: ' + item.ma_sinh_vien : ''}
            </option>
          `);
        });
      });
    }

    taiSanPhongSelect.on('change', function() {
      const selected = $(this).find(':selected');
      const img = selected.data('img');
      const ten = selected.data('ten');
      const nguoi = selected.data('nguoi');
      const maSV = selected.data('masv');
      const maSlot = selected.data('slot');

      previewTaiSan.html(img
        ? `<div class="card p-2 shadow-sm" style="width:250px;margin:0 auto;">
             <img src="${img}" class="card-img-top rounded" style="object-fit:cover;height:180px;">
             <div class="card-body text-center p-2">
               <h6 class="card-title mb-1">${ten}</h6>
               <small class="text-muted">Sử dụng: ${nguoi}</small><br>
               ${maSV ? `<small class="text-muted">Mã SV: ${maSV}</small><br>` : ''}
               ${maSlot ? `<small class="text-muted">Slot: ${maSlot}</small>` : ''}
             </div>
           </div>`
        : '<p class="text-muted">Không có hình ảnh</p>'
      );
    });

    function loadLoaiTaiSanKho() {
      loaiSelect.html('<option>-- Đang tải loại tài sản... --</option>');
      $.get(`/admin/lichbaotri/get-loai-tai-san`, function(data) {
        loaiSelect.html('<option value="">-- Chọn loại tài sản --</option>');
        data.forEach(item => {
          loaiSelect.append(`<option value="${item.id}">${item.ten_loai}</option>`);
        });
      });
    }
  });
</script>
@endif
@endsection
