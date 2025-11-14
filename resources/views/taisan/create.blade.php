@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')
<style>/* 🌿 Tổng thể */
body { background:#f8fafc; }
.page-title { font-weight:700; color:#0c4a6e; }
.card { border:none; border-radius:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05); }

/* 🧩 Nút & form */
.btn { border-radius:999px; }
.btn-success { background:#0ea5e9; border:none; }
.btn-success:hover,
.btn-success:focus { background:#0284c7; }
.btn-success:focus { box-shadow:0 0 0 0.25rem rgba(14,165,233,0.35); outline:none; }
.text-success { color:#0284c7 !important; }
.bg-success { background-color:#0284c7 !important; border-color:#0284c7 !important; }
.badge.bg-success { background-color:#0284c7 !important; }

/* 🧭 Danh sách loại */
#loaiList button {
  border:none; text-align:left; padding:10px 14px;
  background:transparent; border-radius:8px;
  transition:all .15s;
}
#loaiList button:hover { background:#e0f2fe; }
#loaiList button.active {
  background:#0ea5e9; color:#fff; font-weight:600;
}

/* 🪟 Modal */
.modal-content{border-radius:14px;overflow:hidden;border:none;box-shadow:0 8px 24px rgba(0,0,0,0.08)}
.modal-header{background:#0ea5e9;color:#fff}
.asset-list-scroll{max-height:65vh;overflow-y:auto;background:#fff;border-radius:12px;padding:0.35rem;border:1px solid #e5e7eb}
.selected-preview{max-height:65vh;overflow-y:auto;background:#fff;border-radius:12px;padding:0.5rem;border:1px solid #e5e7eb}
.asset-item{display:grid;grid-template-columns:minmax(0,1.2fr) auto;gap:1rem;align-items:center;padding:1rem 1.15rem;border:1px solid #e5e7eb;border-radius:16px;background:#fff;box-shadow:0 10px 18px rgba(15,23,42,.04);transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}
.asset-item:hover{transform:translateY(-2px);box-shadow:0 14px 28px rgba(15,23,42,.08)}
.asset-item__info{display:flex;align-items:center;gap:.85rem}
.asset-item__thumb{width:64px;height:64px;border-radius:16px;object-fit:cover;border:1px solid #e5e7eb;background:#f7fafc}
.asset-item__name{font-weight:600;color:#111827}
.asset-item__code{font-size:.85rem;color:#6b7280}
.asset-item__actions{display:flex;align-items:center;justify-content:flex-end}
.asset-toggle{display:inline-flex;align-items:center;gap:.4rem;border-radius:999px;padding:.45rem 1.05rem;border:1px solid #38bdf8;color:#0284c7;font-weight:600;background:#f0f9ff;transition:all .2s ease}
.asset-toggle:hover{background:#0284c7;color:#fff}
.asset-item.is-selected{border-color:#0284c7;box-shadow:0 18px 32px rgba(2,132,199,.18)}
.asset-item.is-selected .asset-toggle{background:#0284c7;color:#fff;border-color:#0284c7;box-shadow:0 0 0 4px rgba(56,189,248,.18)}
.asset-item.is-selected .asset-toggle:hover{opacity:.9}
.selected-item{display:flex;align-items:center;gap:.5rem;margin-bottom:.65rem;padding:.5rem;border-radius:12px;background:#e0f2fe;border:1px solid #bae6fd}
.selected-item img{width:40px;height:40px;object-fit:cover;border-radius:10px;border:1px solid #bae6fd}
@media (max-width: 991.98px){
  .asset-item{grid-template-columns:minmax(0,1fr);row-gap:.75rem}
  .asset-item__actions{justify-content:flex-start}
}
</style>


<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="page-title mb-1">🧰 Thêm tài sản vào phòng</h3>
      <p class="text-muted small mb-0">Chọn tài sản từ kho và gán vào phòng mong muốn.</p>
    </div>
    <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left me-1"></i> Quay lại</a>
        </div>

    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm">
      <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

  <form id="assetForm" action="{{ route('taisan.store') }}" method="POST" class="card p-4">
        @csrf
    <h5 class="fw-semibold text-success mb-3">Thông tin gán tài sản</h5>

        <div class="mb-3">
      <label class="form-label fw-semibold text-secondary">Tài sản</label><br>
      <button type="button" id="openPickerBtn" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#assetPickerModal">
        <i class="fa fa-search me-1"></i> Chọn tài sản
      </button>
      <div id="selectedAssetsList" class="mt-2 small text-muted">Chưa chọn</div>
        </div>

    <div class="row g-3 mt-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary">Phòng</label>
        <select name="phong_id" class="form-select form-control" required>
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                    @endforeach
                </select>
            </div>

            <!-- ❌ Loại bỏ dropdown tình trạng – hệ thống sẽ tự lấy từ kho -->
        </div>

        <div class="text-end mt-4">
      <button type="submit" class="btn btn-success px-4 py-2 shadow-sm"><i class="fa fa-save me-1"></i> Lưu tài sản</button>
        </div>
    </form>
</div>

{{-- Modal chọn tài sản (giữ nguyên toàn bộ) --}}
<div class="modal fade" id="assetPickerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">🔍 Chọn tài sản</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-3 border-end">
            <h6 class="fw-semibold mb-2">Loại tài sản</h6>
            <div class="list-group loai-list" id="loaiList">
              @foreach($loaiTaiSans as $index => $loai)
                <button class="list-group-item list-group-item-action {{ $index===0 ? 'active' : '' }}" data-id="{{ $loai->id }}">{{ $loai->ten_loai }}</button>
              @endforeach
            </div>
          </div>

          <div class="col-md-6">
            <h6 class="fw-semibold mb-2">Danh sách tài sản</h6>
            <div id="list_taisan" class="asset-list-scroll p-2 border">
              <p class="text-muted text-center mt-3">Chọn loại tài sản để hiển thị...</p>
            </div>
          </div>

          <div class="col-md-3">
            <h6 class="fw-semibold mb-2">Đã chọn (<span id="countSelected">0</span>)</h6>
            <div class="selected-preview border p-2" id="selectedPreview">
              <p class="text-muted small">Chưa chọn tài sản nào</p>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-outline-danger me-auto" id="clearAll"><i class="fa fa-trash me-1"></i> Xóa tất cả</button>
        <button type="button" class="btn btn-success" id="confirmSelection"><i class="fa fa-check me-1"></i> Xác nhận chọn</button>
      </div>
    </div>
  </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
  const debug = (...args) => console.log('[ASSET_PICKER]', ...args);

  // Robust wait: trả về khi selector tồn tại (phục vụ cả trường hợp layout load chậm)
  function waitFor(selector, timeout = 5000) {
    return new Promise((resolve, reject) => {
      const el = document.querySelector(selector);
      if (el) return resolve(el);

      const obs = new MutationObserver(() => {
        const found = document.querySelector(selector);
        if (found) {
          obs.disconnect();
          resolve(found);
        }
      });
      obs.observe(document.documentElement, { childList: true, subtree: true });

      // fallback timeout
      setTimeout(() => {
        obs.disconnect();
        reject(new Error('timeout waiting for ' + selector));
      }, timeout);
    });
  }

  // Gồm toàn bộ logic để không phụ thuộc thứ tự load
  async function init() {
    debug('init start');

    // chờ các element quan trọng (nếu có thể, tăng timeout nếu trang nặng)
    let pickerModalEl, confirmBtn, listTaiSan, selectedPreview, countSelected, clearAllBtn, selectedAssetsList, assetForm, loaiBtnContainer;
    try {
      pickerModalEl = await waitFor('#assetPickerModal', 8000);
      confirmBtn = await waitFor('#confirmSelection', 8000);
      listTaiSan = document.getElementById('list_taisan');
      selectedPreview = document.getElementById('selectedPreview');
      countSelected = document.getElementById('countSelected');
      clearAllBtn = document.getElementById('clearAll');
      selectedAssetsList = document.getElementById('selectedAssetsList');
      assetForm = document.getElementById('assetForm');
      loaiBtnContainer = document.getElementById('loaiList');
      debug('Required DOM nodes found');
    } catch (err) {
      debug('Waiting for DOM failed:', err.message);
            return;
        }
    // confirmBtn exists?
    if (!confirmBtn) {
      debug('confirmSelection element not found — abort');
            return;
   }
    // Avoid double-binding: mark when we've bound
    if (confirmBtn.dataset.bound === '1') {
      debug('confirmSelection already bound — skipping');
    } else {
      confirmBtn.dataset.bound = '1';
      debug('Binding click handler to confirmSelection');
   }
    // Safe bootstrap modal instance (fallback manual close when bootstrap missing)
    let pickerModal = null;
    if (typeof bootstrap !== 'undefined' && pickerModalEl) {
      try {
        pickerModal = bootstrap.Modal.getOrCreateInstance(pickerModalEl);
        debug('Bootstrap modal instance ready');
      } catch (e) {
        debug('Bootstrap modal init error:', e);
      }
    } else {
      debug('Bootstrap not available — will use manual hide fallback');
   }
    const relatedBase = "{{ url('admin/taisan/related') }}";
    const selectedAssets = new Map();

    function updateAssetItemState(container, active) {
      if (!container) return;
      container.classList.toggle('is-selected', active);
      const btn = container.querySelector('.asset-toggle');
      if (!btn) return;
      const label = btn.querySelector('.asset-toggle__label');
      if (label) {
        label.textContent = active ? 'Bỏ chọn' : 'Chọn';
      }
    }
    function upsertSelected(id, payload) {
      if (selectedAssets.has(id)) {
        selectedAssets.delete(id);
      } else {
        selectedAssets.set(id, payload);
      }
    }
    if (listTaiSan) {
      listTaiSan.addEventListener('click', (e) => {
        const btn = e.target.closest('.asset-toggle');
        if (!btn) return;
        const item = btn.closest('.asset-item');
        const id = String(btn.dataset.id || '');
        if (!id) return;
        const payload = {
          id,
          ten_tai_san: btn.dataset.name || id,
          hinh_anh: btn.dataset.img || undefined
        };
        upsertSelected(id, payload);
        updateAssetItemState(item, selectedAssets.has(id));
        renderPreview();
      });
    }
    // render preview helper
    function renderPreview() {
      if (!selectedPreview || !countSelected) return;
      selectedPreview.innerHTML = '';
      if (selectedAssets.size === 0) {
        selectedPreview.innerHTML = '<p class="text-muted small">Chưa chọn tài sản nào</p>';
      } else {
        selectedAssets.forEach(item => {
          const node = document.createElement('div');
          node.className = 'selected-item';
          node.innerHTML = `<img src="${item.hinh_anh || 'https://via.placeholder.com/50'}" width="40" height="40" class="rounded"> <span>${item.ten_tai_san}</span>`;
          selectedPreview.appendChild(node);
        });
      }
      countSelected.textContent = selectedAssets.size;
      debug('Preview updated, count:', selectedAssets.size);
    }
    // Clear all
    if (clearAllBtn) {
      clearAllBtn.addEventListener('click', (ev) => {
        ev.preventDefault();
        selectedAssets.clear();
        listTaiSan?.querySelectorAll('.asset-item').forEach(item => updateAssetItemState(item, false));
        renderPreview();
        debug('Cleared all selections');
      });
    }
    // Confirm selection click (single handler)
    confirmBtn.addEventListener('click', (ev) => {
      ev.preventDefault();
      debug('confirmSelection clicked — building hidden inputs');

      // remove old inputs
      assetForm.querySelectorAll('input[name="tai_san_ids[]"]').forEach(i => i.remove());
      selectedAssetsList.innerHTML = '';

      if (selectedAssets.size === 0) {
        selectedAssetsList.innerHTML = '<span class="text-muted small">Chưa chọn</span>';
      } else {
        selectedAssets.forEach(item => {
          selectedAssetsList.innerHTML += `<span class="badge bg-success me-1 mb-1">${item.ten_tai_san}</span>`;
          const hidden = document.createElement('input');
          hidden.type = 'hidden';
          hidden.name = 'tai_san_ids[]';
          hidden.value = item.id;
          assetForm.appendChild(hidden);
          debug('Added hidden input for', item.id);
        });
      }

      // close modal: use bootstrap if có, otherwise manual fallback
      if (pickerModal && typeof pickerModal.hide === 'function') {
        pickerModal.hide();
        debug('pickerModal.hide() called');
      } else {
        // manual hide fallback
        pickerModalEl.classList.remove('show');
        pickerModalEl.style.display = 'none';
        document.body.classList.remove('modal-open');
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        debug('Manual modal hide applied');
      }
    });
    // Extra: log when modal hidden/shown for debugging
    try {
      pickerModalEl.addEventListener('shown.bs.modal', () => debug('modal shown'));
      pickerModalEl.addEventListener('hidden.bs.modal', () => debug('modal hidden'));
    } catch (e) {
      // ignore if bootstrap events not present
    }
    // If there are any pre-rendered checkboxes (loaded when selecting loại), ensure they're reflecting selectedAssets map
    // Also bind click for loai buttons (if not already)
    if (loaiBtnContainer) {
      loaiBtnContainer.addEventListener('click', (e) => {
        const btn = e.target.closest('button[data-id]');
        if (!btn) return;
        // simulate click handler from original code: fetch and render assets
        (async () => {
          const loaiId = btn.dataset.id;
          debug('loai clicked', loaiId);
          // remove active on siblings
          loaiBtnContainer.querySelectorAll('button[data-id]').forEach(b => b.classList.remove('active'));
          btn.classList.add('active');
          if (!listTaiSan) return;
          listTaiSan.innerHTML = '<p class="text-muted p-2">⏳ Đang tải...</p>';
          try {
            const res = await fetch(`${relatedBase}/${loaiId}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            listTaiSan.innerHTML = '';
            if (!Array.isArray(data) || data.length === 0) {
              listTaiSan.innerHTML = '<p class="text-muted p-2">Không có tài sản thuộc loại này.</p>';
              return;
            }
            data.forEach(item => {
              const div = document.createElement('div');
              div.className = 'asset-item mb-3';
              const id = String(item.id);
              const isSelected = selectedAssets.has(id);
              const img = item.hinh_anh || 'https://via.placeholder.com/96';
              const code = item.ma_tai_san || `TS-${item.id}`;
             div.innerHTML = `
  <div class="asset-item__info">
    <img src="${img}" alt="${item.ten_tai_san}" class="asset-item__thumb">
    <div>
      <div class="asset-item__name">${item.ten_tai_san}</div>
      <div class="asset-item__code">Mã: ${code}</div>
      <div class="asset-item__status small text-muted">Trạng thái: ${item.tinh_trang || 'Chưa có'}</div>
    </div>
  </div>
  <div class="asset-item__actions">
    <button type="button" class="asset-toggle" data-id="${id}">
      <span class="asset-toggle__label">${isSelected ? 'Bỏ chọn' : 'Chọn'}</span>
    </button>
  </div>`;

              listTaiSan.appendChild(div);
              const toggleBtn = div.querySelector('.asset-toggle');
              if (toggleBtn) {
                toggleBtn.dataset.name = item.ten_tai_san || '';
                toggleBtn.dataset.img = img;
              }
              updateAssetItemState(div, isSelected);
            });
            debug('Assets rendered for loai', loaiId);
          } catch (err) {
            listTaiSan.innerHTML = '<p class="text-danger p-2">Lỗi tải dữ liệu!</p>';
            debug('Fetch error', err);
          }
        })();
      });
    }
    // initial render
    renderPreview();
    debug('init complete');
  } // end init

  // start init; if fails, log error
  try {
    init();
  } catch (e) {
    debug('init threw', e);
  }
})();
</script>
@endsection