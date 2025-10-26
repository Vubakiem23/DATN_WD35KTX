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
        
        <div class="table-responsive" style="max-height: 320px; overflow:auto; border:1px solid #eee">
          <table class="table table-sm align-middle mb-0 text-center">
            <thead>
              <tr>
                <th style="width:44px">Chọn</th>
                <th>Mã</th>
                <th>Tên</th>
                <th>Còn trong kho</th>
                <th style="width:140px">Số lượng cấp</th>
              </tr>
            </thead>
            <tbody>
              @foreach($khoTaiSans as $kho)
              <tr>
                <td class="text-center">
                  <input type="checkbox" class="form-check-input asset-check m-0 position-static" data-index="{{ $loop->index }}">
                </td>
                <td>{{ $kho->ma_tai_san ?? ('TS-'.$kho->id) }}</td>
                <td>{{ $kho->ten_tai_san }}</td>
                <td>{{ $kho->so_luong }}</td>
                <td>
                  <input type="number" min="1" max="{{ $kho->so_luong }}" class="form-control form-control-sm text-center asset-qty" data-index="{{ $loop->index }}" placeholder="0">
                  <input type="hidden" name="assets[{{ $kho->id }}]" value="" class="asset-hidden" data-index="{{ $loop->index }}">
                </td>
              </tr>
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
                  const qtyInput = document.querySelector('.asset-qty[data-index="'+idx+'"]');
                  const hidden = document.querySelector('.asset-hidden[data-index="'+idx+'"]');
                  if(this.checked){
                    hidden.value = qtyInput.value && parseInt(qtyInput.value,10)>0 ? parseInt(qtyInput.value,10) : '';
                    qtyInput.disabled = false;
                  } else {
                    hidden.value = '';
                    qtyInput.disabled = true;
                  }
                });
              });
              const qtys = document.querySelectorAll('.asset-qty');
              qtys.forEach(inp => {
                inp.disabled = true;
                inp.addEventListener('input', function(){
                  const idx = this.getAttribute('data-index');
                  const chk = document.querySelector('.asset-check[data-index="'+idx+'"]');
                  const hidden = document.querySelector('.asset-hidden[data-index="'+idx+'"]');
                  if(chk.checked){
                    hidden.value = this.value && parseInt(this.value,10)>0 ? parseInt(this.value,10) : '';
                  }
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
    <select name="khu" class="form-control @error('khu') is-invalid @enderror" required>
      <option value="">--Chọn khu--</option>
      @foreach(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'] as $k)
        <option value="{{ $k }}" {{ old('khu', $phong->khu ?? '') == $k ? 'selected' : '' }}>Khu {{ $k }}</option>
      @endforeach
    </select>
    @error('khu')
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
    <select name="gioi_tinh" class="form-control @error('gioi_tinh') is-invalid @enderror" required>
      <option value="">--Chọn giới tính--</option>
      <option value="Nam" {{ old('gioi_tinh', $phong->gioi_tinh ?? '') == 'Nam' ? 'selected' : '' }}>Nam</option>
      <option value="Nữ" {{ old('gioi_tinh', $phong->gioi_tinh ?? '') == 'Nữ' ? 'selected' : '' }}>Nữ</option>
      <option value="Cả hai" {{ old('gioi_tinh', $phong->gioi_tinh ?? '') == 'Cả hai' ? 'selected' : '' }}>Cả hai</option>
    </select>
    @error('gioi_tinh')
      <div class="invalid-feedback">{{ $message }}</div>
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
  <button class="btn btn-success" type="submit">
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
});
</script>
@endpush

