@csrf

{{-- Hiển thị lỗi chung --}}
@if($errors->any())
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Có lỗi xảy ra:</strong>
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
@endif

<div class="row g-3">
@isset($khoTaiSans)
  <div class="col-12 col-lg-5 order-lg-1 order-2">
    <div class="card h-100">
      <div class="card-body">
        <h5>Chọn tài sản từ kho cấp cho phòng (tùy chọn)</h5>
        @push('styles')
        <style>
          .ts-thumb{width:42px;height:42px;object-fit:cover;border-radius:6px;border:1px solid #e9ecef}
          .group-row .ts-thumb{width:28px;height:28px;border-radius:50%;margin-right:8px}
          .group-row strong{display:inline-flex;align-items:center;gap:.4rem}
          /* Làm dịu phần tiêu đề bảng và các vùng bôi xanh của theme */
          .card .table thead th{
            background:#f8f9fa !important;
            color:#495057 !important;
            font-weight:600;
            border-bottom:1px solid #e9ecef !important;
          }
          .group-row{background:#f8f9fa !important;}
          .group-row .toggle-hint{color:#6c757d !important;}
          .badge.badge-soft{
            background:#fff !important;
            color:#495057 !important;
            border:1px solid #e9ecef;
            font-weight:500;
          }
          /* Ẩn header chính; dùng sub-header riêng dưới từng nhóm */
          #khoWrap thead{display:none}
          .subhead-row{background:#f8f9fa;font-weight:600;color:#495057}
        </style>
        @endpush
        
        <div id="khoWrap" class="table-responsive" style="max-height: 320px; overflow:auto; border:1px solid #eee">
          <table class="table table-sm align-middle mb-0 text-center">
            <thead>
              <tr>
                <th style="width:44px">Chọn</th>
                <th>Ảnh</th>
                <th>Mã</th>
                <th>Tên</th>
                <th>Còn trong kho</th>
                <th>Kho</th>
                <th style="width:140px">Số lượng cấp</th>
              </tr>
            </thead>
            <tbody>
              @php $groups = collect($khoTaiSans)->groupBy('loai_id'); $idx = 0; @endphp
              @foreach($groups as $loaiId => $items)
              @php $first = $items->first(); $tenLoai = $first->ten_tai_san ?? ('Loại '.$loaiId); $tong = $items->sum('so_luong'); @endphp
              @php $firstImg = $first->hinh_anh ? asset('storage/'.$first->hinh_anh) : null; @endphp
              <tr class="group-row" data-group="{{ $loaiId }}" style="background:#f8f9fa;">
                <td colspan="6" class="text-start">
                  <strong>
                    @if($firstImg)
                      <img src="{{ $firstImg }}" alt="thumb" class="ts-thumb">
                    @else
                      <svg class="ts-thumb" viewBox="0 0 24 24"><rect width="24" height="24" fill="#f1f3f5"/><path d="M3 16l5-6 4 5 6-8 3 4" stroke="#adb5bd" fill="none" stroke-width="2"/></svg>
                    @endif
                    {{ $tenLoai }}
                  </strong>
                  <span class="badge badge-soft ms-2">Tổng còn trong kho: {{ $tong }}</span>
                  <button type="button" class="btn btn-sm btn-outline-primary float-end btn-toggle-group" data-group="{{ $loaiId }}">Xem kho</button>
                </td>
              </tr>
              <tr class="subhead-row" data-group="{{ $loaiId }}" style="display:none">
                <td>Chọn</td>
                <td>Ảnh</td>
                <td>Mã</td>
                <td>Tên</td>
                <td>Còn trong kho</td>
                <td>Tình trạng</td>
              </tr>
                @foreach($items as $kho)
                <tr class="child-row" data-group="{{ $loaiId }}" style="display:none">
                  <td class="text-center">
                    <input type="checkbox" class="form-check-input asset-check m-0 position-static" data-index="{{ $idx }}" data-qty="{{ $kho->so_luong ?? 1 }}">
                  </td>
                  <td>
                    @if($kho->hinh_anh)
                      <img src="{{ asset('storage/'.$kho->hinh_anh) }}" alt="ảnh" class="ts-thumb">
                    @else
                      <svg class="ts-thumb" viewBox="0 0 24 24"><rect width="24" height="24" fill="#f1f3f5"/><path d="M3 16l5-6 4 5 6-8 3 4" stroke="#adb5bd" fill="none" stroke-width="2"/></svg>
                    @endif
                  </td>
                  <td>{{ $kho->ma_tai_san ?? ('TS-'.$kho->id) }}</td>
                  <td>{{ $kho->ten_tai_san }}</td>
                  <td>{{ $kho->so_luong }}</td>
                  <td>{{ $kho->tinh_trang ?? '-' }}</td>
                  <input type="hidden" name="assets[{{ $kho->id }}]" value="" class="asset-hidden" data-index="{{ $idx }}">
                </tr>
                @php $idx++; @endphp
                @endforeach
              @endforeach
            </tbody>
          </table>
  
          @push('scripts')
          <script>
            document.addEventListener('DOMContentLoaded', function(){
              const checks = document.querySelectorAll('.asset-check');
              checks.forEach(chk => {
                chk.addEventListener('change', function(){
                  const idx = this.getAttribute('data-index');
                  const hidden = document.querySelector('.asset-hidden[data-index="'+idx+'"]');
                  if(this.checked){
                    const qty = parseInt(this.getAttribute('data-qty')||'1',10);
                    hidden.value = qty > 0 ? qty : 1;
                  } else {
                    hidden.value = '';
                  }
                });
              });

              // Toggle bằng nút "Xem kho" ở hàng cha
              document.querySelectorAll('.btn-toggle-group').forEach(btn => {
                btn.addEventListener('click', function(e){
                  const gid = this.getAttribute('data-group');
                  const children = document.querySelectorAll('.child-row[data-group="'+gid+'"]');
                  const subhead = document.querySelector('.subhead-row[data-group="'+gid+'"]');
                  const hidden = Array.from(children).every(tr => tr.style.display === 'none');
                  if (subhead){ subhead.style.display = hidden ? '' : 'none'; }
                  children.forEach(tr => tr.style.display = hidden ? '' : 'none');
                  this.textContent = hidden ? 'Thu gọn' : 'Xem kho';
                });
              });
            });
          </script>
          @endpush
        </div>
      </div>
    </div>
  </div>
@endisset

  <div class="col-12 {{ isset($khoTaiSans) ? 'col-lg-7' : 'col-lg-12' }} order-lg-2 order-1">
<div class="mb-3">
  <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
  <input type="text" name="ten_phong" value="{{ old('ten_phong', $phong->ten_phong ?? '') }}" class="form-control @error('ten_phong') is-invalid @enderror" required>
  @error('ten_phong')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Khu <span class="text-danger">*</span></label>
    <select name="khu_id" class="form-control @error('khu_id') is-invalid @enderror" required {{ (isset($khus) && count($khus)) ? '' : 'disabled' }}>
      @if(isset($khus) && count($khus))
        <option value="">--Chọn khu--</option>
        @foreach(($khus ?? []) as $khu)
          <option value="{{ $khu->id }}" 
            {{ (string)old('khu_id', $phong->khu_id ?? '') === (string)$khu->id ? 'selected' : '' }}
            data-gender="{{ $khu->gioi_tinh }}"
            data-name="{{ $khu->ten_khu }}"
          >{{ $khu->ten_khu }} ({{ $khu->gioi_tinh }})</option>
        @endforeach
      @else
        <option value="">Chưa có khu — vào Quản lý khu để tạo trước</option>
      @endif
    </select>
    @error('khu_id')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Loại phòng</label>
    <select name="loai_phong_display" class="form-control @error('loai_phong') is-invalid @enderror" disabled>
      <option value="">--Chọn--</option>
      <option value="Đơn" {{ old('loai_phong', $phong->loai_phong ?? '') == 'Đơn' ? 'selected' : '' }}>Đơn</option>
      <option value="Đôi" {{ old('loai_phong', $phong->loai_phong ?? '') == 'Đôi' ? 'selected' : '' }}>Đôi</option>
      {{-- Từ 3 trở lên đặt theo số giường: Phòng N --}}
      @for($i=3;$i<=12;$i++)
        <option value="Phòng {{ $i }}" {{ old('loai_phong', $phong->loai_phong ?? '') == ('Phòng '.$i) ? 'selected' : '' }}>Phòng {{ $i }}</option>
      @endfor
    </select>
    <input type="hidden" name="loai_phong" value="{{ old('loai_phong', $phong->loai_phong ?? '') }}">
    @error('loai_phong')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Giới tính <span class="text-danger">*</span></label>
    <input type="text" class="form-control" name="gioi_tinh_display" value="{{ old('gioi_tinh', $phong->gioi_tinh ?? '') }}" readonly>
    <input type="hidden" name="gioi_tinh" value="{{ old('gioi_tinh', $phong->gioi_tinh ?? '') }}">
    @error('gioi_tinh')
      <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
  </div>
</div>

<div class="row">
  <div class="col-md-4 mb-3">
    <label class="form-label">Sức chứa <span class="text-danger">*</span></label>
    <input type="number" name="suc_chua" value="{{ old('suc_chua', $phong->suc_chua ?? 1) }}" class="form-control @error('suc_chua') is-invalid @enderror" min="1" max="20" required>
    @error('suc_chua')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Giá phòng (VND/tháng) <span class="text-danger">*</span></label>
    <input type="number" name="gia_phong" value="{{ old('gia_phong', $phong->gia_phong ?? 0) }}" class="form-control @error('gia_phong') is-invalid @enderror" min="0" step="1" required>
    @error('gia_phong')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
    <select name="trang_thai" class="form-control @error('trang_thai') is-invalid @enderror" required>
      <option value="">--Chọn trạng thái--</option>
      <option value="Trống" {{ old('trang_thai', $phong->trang_thai ?? '') == 'Trống' ? 'selected' : '' }}>Trống</option>
      <option value="Đã ở" {{ old('trang_thai', $phong->trang_thai ?? '') == 'Đã ở' ? 'selected' : '' }}>Đã ở</option>
      <option value="Bảo trì" {{ old('trang_thai', $phong->trang_thai ?? '') == 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
    </select>
    @error('trang_thai')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  <div class="col-md-4 mb-3">
    <label class="form-label">Ghi chú</label>
    <input type="text" name="ghi_chu" value="{{ old('ghi_chu', $phong->ghi_chu ?? '') }}" class="form-control @error('ghi_chu') is-invalid @enderror">
    @error('ghi_chu')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>
</div>

{{-- Upload ảnh --}}
<div class="mb-3">
  <label class="form-label">Ảnh phòng (tùy chọn)</label>
  <input type="file" name="hinh_anh" class="form-control @error('hinh_anh') is-invalid @enderror" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
  
  @error('hinh_anh')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
  
  @if(isset($phong) && $phong->hinh_anh)
    <div class="mt-2">
      <img src="{{ asset('storage/' . $phong->hinh_anh) }}" alt="Ảnh phòng hiện tại" class="img-thumbnail" style="max-width: 200px;">
      <p class="text-muted small mt-1">Ảnh hiện tại (sẽ được thay thế nếu upload ảnh mới)</p>
    </div>
  @endif
</div>
<div class="mb-3">
  <button class="btn btn-success" type="submit" {{ (isset($khus) && count($khus)) ? '' : 'disabled' }}>
    <i class="fas fa-save"></i> Lưu
  </button>
  <a href="{{ route('phong.index') }}" class="btn btn-secondary">
    <i class="fas fa-times"></i> Hủy
  </a>
</div>

  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const capacityInput = document.querySelector('input[name="suc_chua"]');
  const typeSelect = document.querySelector('select[name="loai_phong_display"]');
  const typeHidden = document.querySelector('input[name="loai_phong"]');
  const khuSelect = document.querySelector('select[name="khu_id"]');
  const genderHidden = document.querySelector('input[name="gioi_tinh"]');
  const genderDisplay = document.querySelector('input[name="gioi_tinh_display"]');
  if(!capacityInput || !typeSelect) return;

  const computeLabel = (n) => {
    const cap = parseInt(n, 10);
    if (isNaN(cap) || cap <= 0) return '';
    if (cap === 1) return 'Đơn';
    if (cap === 2) return 'Đôi';
    return 'Phòng ' + cap;
  };

  const applyTypeFromCapacity = () => {
    const label = computeLabel(capacityInput.value);
    if (!label) return;
    // If option not exists (e.g. capacity > 12), add it temporarily
    let opt = Array.from(typeSelect.options).find(o => o.value === label);
    if (!opt) {
      opt = new Option(label, label);
      typeSelect.add(opt);
    }
    typeSelect.value = label;
    if (typeHidden) typeHidden.value = label;
  };

  // Initial apply on load
  applyTypeFromCapacity();

  // Update on change/input
  capacityInput.addEventListener('input', applyTypeFromCapacity);
  capacityInput.addEventListener('change', applyTypeFromCapacity);

  // Sync gender from selected Khu
  if (khuSelect && genderHidden && genderDisplay) {
    const syncGender = () => {
      const opt = khuSelect.options[khuSelect.selectedIndex];
      const g = opt ? (opt.getAttribute('data-gender') || '') : '';
      genderHidden.value = g;
      genderDisplay.value = g;
    };
    syncGender();
    khuSelect.addEventListener('change', syncGender);
  }
});
</script>
@endpush

