@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')
<style>/* 🌿 Tổng thể */
body { background:#f8fafc; }
.page-title { font-weight:700; color:#155724; }
.card { border:none; border-radius:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05); }

/* 🧩 Nút & form */
.btn { border-radius:999px; }
.btn-success { background:#198754; border:none; }
.btn-success:hover { background:#157347; }

/* 📦 Item tài sản */
.asset-item {
  display:flex; align-items:center; gap:12px;
  padding:10px 14px; border:1px solid #e5e7eb; border-radius:12px;
  background:#fff; transition:all .15s ease;
  cursor:pointer;
}
.asset-item:hover { transform:translateY(-2px); box-shadow:0 2px 6px rgba(0,0,0,.05); }
.asset-img { width:60px; height:60px; object-fit:cover; border-radius:10px; border:1px solid #e2e8f0; }

/* ✅ Preview */
.selected-item {
  display:flex; gap:8px; align-items:center;
  padding:6px 10px; background:#e8f5e9; border-radius:8px;
  margin-bottom:6px; border:1px solid #d4edda;
}
.selected-item img { border-radius:6px; }

/* 🧭 Danh sách loại */
#loaiList button {
  border:none; text-align:left; padding:10px 14px;
  background:transparent; border-radius:8px;
  transition:all .15s;
}
#loaiList button:hover { background:#e9f7ef; }
#loaiList button.active {
  background:#198754; color:#fff; font-weight:600;
}

/* ⚙️ Debug box */
.debug-box {
  position:fixed; right:16px; bottom:16px;
  width:280px; max-height:40vh; overflow:auto;
  background:#fff; border:1px solid #dee2e6;
  box-shadow:0 6px 18px rgba(0,0,0,.06);
  padding:8px 12px; border-radius:10px;
  font-size:12px; z-index:2000; color:#0f5132;
}
.debug-box h6 { font-size:13px; margin-bottom:4px; }
.badge-debug {
  display:inline-block; margin:2px 4px 2px 0;
  padding:3px 8px; background:#f1f3f5;
  border-radius:999px; color:#0f5132; border:1px solid #d1e7dd;
}

/* 🪟 Modal */
.modal-content {
  border-radius:14px; overflow:hidden;
  border:none; box-shadow:0 8px 24px rgba(0,0,0,0.08);
}
.modal-header { background:#198754; color:#fff; }
.asset-list-scroll { max-height:65vh; overflow-y:auto; background:#fff; border-radius:8px; }
.selected-preview { max-height:65vh; overflow-y:auto; background:#fff; }
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
      <div class="col-md-6">
        <label class="form-label fw-semibold text-secondary">Tình trạng khi gán</label>
        <select name="tinh_trang" class="form-select form-control" required>
          <option value="Bình thường">Bình thường</option>
          <option value="Hỏng">Hỏng</option>
          <option value="Cần bảo trì">Cần bảo trì</option>
        </select>
      </div>
    </div>
    <div class="text-end mt-4">
      <button type="submit" class="btn btn-success px-4 py-2 shadow-sm"><i class="fa fa-save me-1"></i> Lưu tài sản</button>
    </div>
  </form>
</div>
{{-- 🧭 Modal chọn tài sản --}}
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
{{-- 🧩 Debug box --}}
<div class="debug-box" id="debugBox" aria-live="polite">
  <h6>Debug</h6>
  <div id="debugContent"><span class="badge-debug">Init</span></div>
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

    // If asset list exists, ensure checkboxes delegate works (delegation on listTaiSan)
    if (listTaiSan) {
      listTaiSan.addEventListener('change', (e) => {
        const target = e.target;
        if (!target) return;
        if (target.matches('input[type="checkbox"]')) {
          const id = target.value;
          const label = target.closest('.asset-item')?.querySelector('label')?.innerText || id;
          // minimal item representation
          if (target.checked) {
            selectedAssets.set(id, { id: id, ten_tai_san: label.trim(), hinh_anh: target.closest('.asset-item')?.querySelector('img')?.src });
            debug('checked', id);
          } else {
            selectedAssets.delete(id);
            debug('unchecked', id);
          }
          renderPreview();
        }
      });
   }    // render preview helper
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
        document.querySelectorAll('#list_taisan input[type="checkbox"]').forEach(chk => chk.checked = false);
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
              div.className = 'asset-item mb-2';
              const checked = selectedAssets.has(String(item.id)) ? 'checked' : '';
              div.innerHTML = `
                <input class="form-check-input" type="checkbox" value="${item.id}" id="ts_${item.id}" ${checked}>
                <img src="${item.hinh_anh || 'https://via.placeholder.com/70'}" class="asset-img">
                <label for="ts_${item.id}"><strong>${item.ma_tai_san ?? '---'}</strong> - ${item.ten_tai_san}</label>`;
              listTaiSan.appendChild(div);
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