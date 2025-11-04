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
          .asset-picker{display:flex;flex-direction:column;gap:1rem;max-height:320px;overflow:auto;padding-right:.25rem}
          .asset-picker::-webkit-scrollbar{width:6px}
          .asset-picker::-webkit-scrollbar-thumb{background:#ced4da;border-radius:10px}
          .asset-group{border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 6px 18px rgba(15,23,42,.06);transition:box-shadow .2s ease,border-color .2s ease}
          .asset-group.is-open{box-shadow:0 10px 24px rgba(15,23,42,.1);border-color:#d1d5db}
          .asset-group__header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.25rem;background:#f8fafc;border-bottom:1px solid #e5e7eb;flex-wrap:wrap}
          .asset-group__title{display:flex;align-items:center;gap:1rem;min-width:200px}
          .asset-thumb{width:52px;height:52px;border-radius:14px;object-fit:cover;border:1px solid #e5e7eb;background:#f1f5f9}
          .asset-thumb--sm{width:40px;height:40px;border-radius:12px}
          .asset-group__name{font-weight:600;color:#1f2937;margin-bottom:.15rem}
          .asset-group__meta{display:flex;align-items:center;gap:.75rem}
          .asset-group__meta .badge{background:#fff;color:#1f2937;border:1px solid #d1d5db;font-weight:500;border-radius:999px;padding:.35rem .75rem}
          .asset-group__actions{display:flex;align-items:center;gap:.5rem}
          .asset-group__actions .btn{white-space:nowrap}
          .asset-list{display:none;flex-direction:column;gap:.75rem;padding:1rem 1.25rem;background:#fff}
          .asset-list.show{display:flex}
          .asset-item{display:grid;grid-template-columns:minmax(0,1.2fr) auto auto;gap:1rem;align-items:center;padding:1rem 1.15rem;border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 10px 18px rgba(15,23,42,.04);transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
          .asset-item:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(15,23,42,.08)}
          .asset-item__info{display:flex;align-items:center;gap:.85rem}
          .asset-item__name{font-weight:600;color:#111827}
          .asset-item__code{font-size:.85rem;color:#6b7280}
          .asset-item__extras{text-align:right;display:flex;flex-direction:column;gap:.35rem;margin-right:.5rem}
          .asset-item__extras .badge{background:#e0f2fe;color:#0369a1;border:none;font-weight:600;border-radius:999px;padding:.35rem .65rem}
          .asset-item__extras .status{font-size:.85rem;color:#6b7280}
          .asset-item__actions{display:flex;align-items:center;justify-content:flex-end}
          .asset-toggle{display:inline-flex;align-items:center;gap:.4rem;border-radius:999px;padding:.45rem 1.15rem;font-weight:600;border:1px solid #c7d2fe;background:#eef2ff;color:#3730a3;transition:background .2s ease,color .2s ease,box-shadow .2s ease,border-color .2s ease;cursor:pointer}
          .asset-toggle svg{width:16px;height:16px}
          .asset-item.is-selected{border-color:#6366f1;box-shadow:0 18px 32px rgba(79,70,229,.18)}
          .asset-item.is-selected .asset-toggle{background:#4f46e5;color:#fff;border-color:#4f46e5;box-shadow:0 0 0 4px rgba(99,102,241,.18)}
          .asset-item .asset-hidden{display:none}
          @media (max-width: 991.98px){
            .asset-picker{max-height:none;padding-right:0}
            .asset-item{grid-template-columns:minmax(0,1fr) auto;align-items:flex-start}
            .asset-item__extras{margin-right:0}
            .asset-item__actions{grid-column:1 / -1;justify-content:flex-start}
          }
          @media (max-width: 767.98px){
            .asset-group__header{align-items:flex-start}
            .asset-group__meta{width:100%;justify-content:space-between}
          }
          @media (max-width: 575.98px){
            .asset-item{grid-template-columns:1fr;padding:.9rem .9rem}
            .asset-item{row-gap:.65rem}
            .asset-item__extras{text-align:left}
            .asset-item__extras{grid-column:1 / -1}
            .asset-item__actions{grid-column:1 / -1;justify-content:flex-start}
          }
        </style>
        @endpush

        <div id="khoWrap" class="asset-picker">
          @php $groups = collect($khoTaiSans)->groupBy('loai_id'); $idx = 0; @endphp
          @foreach($groups as $loaiId => $items)
            @php $first = $items->first(); $tenLoai = $first->ten_tai_san ?? ('Loại '.$loaiId); $tong = $items->sum('so_luong'); @endphp
            @php $firstImg = $first->hinh_anh ? asset('storage/'.$first->hinh_anh) : null; @endphp
            <div class="asset-group" data-group="{{ $loaiId }}">
              <div class="asset-group__header">
                <div class="asset-group__title">
                  @if($firstImg)
                    <img src="{{ $firstImg }}" alt="thumb" class="asset-thumb">
                  @else
                    <svg class="asset-thumb" viewBox="0 0 48 48"><rect width="48" height="48" rx="14" fill="#f1f5f9"/><path d="M10 32l8-10 6 8 10-14 4 6" stroke="#cbd5f5" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  @endif
                  <div>
                    <div class="asset-group__name">{{ $tenLoai }}</div>
                    <div class="text-muted small">Có {{ $items->count() }} tài sản thuộc nhóm này</div>
                  </div>
                </div>
                <div class="asset-group__actions">
                  <span class="badge">Tổng còn: {{ $tong }}</span>
                  <button type="button" class="btn btn-outline-primary btn-sm btn-toggle-group" data-group="{{ $loaiId }}">
                    Xem kho
                  </button>
                </div>
              </div>

              <div class="asset-list" data-group="{{ $loaiId }}">
                @foreach($items as $kho)
                  <div class="asset-item" data-group="{{ $loaiId }}">
                    <div class="asset-item__info">
                      @if($kho->hinh_anh)
                        <img src="{{ asset('storage/'.$kho->hinh_anh) }}" alt="ảnh" class="asset-thumb asset-thumb--sm">
                      @else
                        <svg class="asset-thumb asset-thumb--sm" viewBox="0 0 40 40"><rect width="40" height="40" rx="12" fill="#f1f5f9"/><path d="M9 27l6-7 4 5 7-10 3 4" stroke="#cbd5f5" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      @endif
                      <div>
                        <div class="asset-item__name">{{ $kho->ten_tai_san }}</div>
                        <div class="asset-item__code">Mã: {{ $kho->ma_tai_san ?? ('TS-'.$kho->id) }}</div>
                      </div>
                    </div>
                    <div class="asset-item__extras">
                      <span class="badge">Còn trong kho: {{ $kho->so_luong }}</span>
                      <div class="status">Tình trạng: {{ $kho->tinh_trang ?? '-' }}</div>
                    </div>
                    <div class="asset-item__actions">
                      <button type="button"
                              class="asset-toggle"
                              data-index="{{ $idx }}"
                              data-qty="{{ $kho->so_luong ?? 1 }}"
                              data-label-default="Chọn"
                              data-label-active="Đã chọn">
                        <svg viewBox="0 0 16 16" fill="none"><path d="M3.5 8.5l2.5 2.5 6-6" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <span>Chọn</span>
                      </button>
                    </div>
                    <input type="hidden" name="assets[{{ $kho->id }}]" value="{{ old('assets.'.$kho->id, '') }}" class="asset-hidden" data-index="{{ $idx }}">
                  </div>
                  @php $idx++; @endphp
                @endforeach
              </div>
            </div>
              @endforeach
  
          @push('scripts')
          <script>
            document.addEventListener('DOMContentLoaded', function(){
              const toggles = document.querySelectorAll('.asset-toggle');

              toggles.forEach(btn => {
                const idx = btn.getAttribute('data-index');
                const qty = parseInt(btn.getAttribute('data-qty') || '1', 10);
                const hidden = document.querySelector('.asset-hidden[data-index="' + idx + '"]');
                const item = btn.closest('.asset-item');
                const defaultLabel = btn.getAttribute('data-label-default') || 'Chọn';
                const activeLabel = btn.getAttribute('data-label-active') || 'Đã chọn';
                const labelSpan = btn.querySelector('span');

                const applyState = (isSelected) => {
                  if(!item || !hidden) return;
                  item.classList.toggle('is-selected', isSelected);
                  btn.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
                  if(labelSpan){
                    labelSpan.textContent = isSelected ? activeLabel : defaultLabel;
                  }
                  if(isSelected){
                    const fallbackQty = qty > 0 ? qty : 1;
                    const current = parseInt(hidden.value, 10);
                    hidden.value = !isNaN(current) && current > 0 ? current : fallbackQty;
                  } else {
                    hidden.value = '';
                  }
                };

                btn.addEventListener('click', () => {
                  const willSelect = !item.classList.contains('is-selected');
                  applyState(willSelect);
                });

                if(hidden && hidden.value){
                  applyState(true);
                } else {
                  applyState(false);
                }
              });

              document.querySelectorAll('.btn-toggle-group').forEach((btn, index) => {
                const gid = btn.getAttribute('data-group');
                const groupCard = btn.closest('.asset-group');
                const list = document.querySelector('.asset-list[data-group="' + gid + '"]');

                const toggleGroup = (forceOpen = null) => {
                  if(!list || !groupCard) return;
                  const shouldOpen = forceOpen !== null ? forceOpen : !list.classList.contains('show');
                  list.classList.toggle('show', shouldOpen);
                  groupCard.classList.toggle('is-open', shouldOpen);
                  btn.textContent = shouldOpen ? 'Thu gọn' : 'Xem kho';
                };

                btn.addEventListener('click', () => toggleGroup());

                const hasSelected = list && list.querySelector('.asset-item.is-selected');
                if(index === 0 || hasSelected){
                  toggleGroup(true);
                }
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
      @for($i=3;$i<=8;$i++)
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
    <input type="number" name="suc_chua" value="{{ old('suc_chua', $phong->suc_chua ?? 1) }}" class="form-control @error('suc_chua') is-invalid @enderror" min="1" max="8" required>
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
    @php $defaultStatus = old('trang_thai', $phong->trang_thai ?? 'Trống'); @endphp
    @if(isset($phong))
    <select name="trang_thai" class="form-control @error('trang_thai') is-invalid @enderror" required>
      <option value="">--Chọn trạng thái--</option>
        <option value="Trống" {{ $defaultStatus === 'Trống' ? 'selected' : '' }}>Trống</option>
        <option value="Đã ở" {{ $defaultStatus === 'Đã ở' ? 'selected' : '' }}>Đã ở</option>
        <option value="Bảo trì" {{ $defaultStatus === 'Bảo trì' ? 'selected' : '' }}>Bảo trì</option>
    </select>
    @else
      <input type="hidden" name="trang_thai" value="Trống">
      <input type="text" class="form-control" value="Trống" readonly>
      <div class="form-text">Phòng mới luôn bắt đầu ở trạng thái Trống.</div>
    @endif
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

  const MAX_CAPACITY = 8;
  const MIN_CAPACITY = parseInt(capacityInput.getAttribute('min'), 10) || 1;

  const sanitizeCapacity = () => {
    const raw = capacityInput.value;
    if (raw === '') return null;
    let cap = parseInt(raw, 10);
    if (isNaN(cap)) return null;
    if (cap > MAX_CAPACITY) {
      cap = MAX_CAPACITY;
      capacityInput.value = cap;
    } else if (cap < MIN_CAPACITY) {
      cap = MIN_CAPACITY;
      capacityInput.value = cap;
    }
    return cap;
  };

  const computeLabel = (cap) => {
    if (!cap || cap <= 0) return '';
    if (cap === 1) return 'Đơn';
    if (cap === 2) return 'Đôi';
    return 'Phòng ' + cap;
  };

  const applyTypeFromCapacity = () => {
    const cap = sanitizeCapacity();
    if (cap === null) return;
    const label = computeLabel(cap);
    if (!label) return;
    // If option not exists (e.g. legacy capacity > 8), add it temporarily
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

