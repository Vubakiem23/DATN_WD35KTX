@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')
<style>
    /* Styles ngắn gọn */
    .page-title {
        font-weight: 700;
        color: #1e293b;
    }

    .asset-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        transition: all .15s;
    }

    .asset-item:hover {
        transform: translateY(-2px);
        background: #f8fafc;
    }

    .asset-img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .selected-item {
        display: flex;
        gap: 8px;
        align-items: center;
        padding: 6px 8px;
        background: #f1f5f9;
        border-radius: 8px;
        margin-bottom: 6px;
    }

    .debug-box {
        position: fixed;
        right: 16px;
        bottom: 16px;
        width: 320px;
        max-height: 40vh;
        overflow: auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
        z-index: 2000;
    }

    .debug-box h6 {
        margin: 0 0 6px 0;
        font-size: 13px;
    }

    .badge-debug {
        display: inline-block;
        margin: 2px 4px 2px 0;
        padding: 4px 8px;
        background: #f8fafc;
        border-radius: 999px;
        color: #0f5132;
        border: 1px solid #cfeadf;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="page-title mb-1">🧰 Thêm tài sản vào phòng</h3>
            <p class="text-muted small mb-0">Chọn tài sản bằng modal — debug tích hợp giúp bạn thấy lỗi ngay.</p>
        </div>
        <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary rounded-pill"><i class="fa fa-arrow-left me-1"></i>Quay lại</a>
    </div>

    {{-- hiển thị lỗi validation --}}
    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm">
        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <form id="assetForm" action="{{ route('taisan.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        <h5 class="fw-semibold text-success mb-3">Thông tin gán tài sản vào phòng</h5>

        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary">Tài sản</label><br>
            <button type="button" id="openPickerBtn" class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#assetPickerModal">🔍 Chọn tài sản</button>
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
            <button type="submit" class="btn btn-success px-4 py-2 rounded-pill shadow-sm"> Lưu tài sản vào phòng</button>
        </div>
    </form>
</div>

{{-- Modal --}}
<div class="modal fade modal-asset-picker" id="assetPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"> Chọn tài sản</h5>
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
                        <div id="list_taisan" class="asset-list-scroll border rounded p-2">
                            <p class="text-muted">Chọn loại tài sản để hiển thị...</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <h6 class="fw-semibold mb-2">Đã chọn (<span id="countSelected">0</span>)</h6>
                        <div class="selected-preview border rounded p-2" id="selectedPreview">
                            <p class="text-muted small">Chưa chọn tài sản nào</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger me-auto" id="clearAll"> Xóa tất cả</button>
                <button type="button" class="btn btn-success" id="confirmSelection"> Xác nhận chọn</button>
            </div>
        </div>
    </div>
</div>



{{-- Bootstrap bundle (bắt buộc) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    (function() {
        // helper debug: write cả vào console + debug box
        function dbg(msg, type = 'info') {
            console.log('[ASSET_PICKER]', msg);
            const box = document.getElementById('debugContent');
            if (!box) return;
            const el = document.createElement('div');
            el.innerHTML = `<span class="badge-debug">${msg}</span>`;
            box.prepend(el);
            // trim to 20 items
            while (box.children.length > 40) box.removeChild(box.lastChild);
        }

        document.addEventListener('DOMContentLoaded', async () => {
            dbg('DOMContentLoaded');

            // selectors
            const loaiList = document.getElementById('loaiList');
            const loaiBtns = loaiList ? loaiList.querySelectorAll('button[data-id]') : [];
            const listTaiSan = document.getElementById('list_taisan');
            const selectedPreview = document.getElementById('selectedPreview');
            const countSelected = document.getElementById('countSelected');
            const clearAllBtn = document.getElementById('clearAll');
            const confirmBtn = document.getElementById('confirmSelection');
            const openPickerBtn = document.getElementById('openPickerBtn');
            const selectedAssetsList = document.getElementById('selectedAssetsList');
            const assetForm = document.getElementById('assetForm');
            const pickerModalEl = document.getElementById('assetPickerModal');
            pickerModalEl.addEventListener('hidden.bs.modal', () => {
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
            });

            // kiểm tra các phần tử quan trọng
            if (!pickerModalEl) {
                dbg('Modal element not found', 'error');
                return;
            }
            if (!confirmBtn) {
                dbg('Confirm button not found', 'error');
                return;
            }
            if (!listTaiSan) {
                dbg('list_taisan not found', 'error');
                return;
            }

            // check bootstrap
            if (typeof bootstrap === 'undefined') {
                dbg('Bootstrap JS chưa load! Hãy include bootstrap.bundle.min.js trước script này', 'error');
                // vẫn tiếp tục nhưng modal hide/show sẽ fallback
            }

            let pickerModal = null;
            try {
                pickerModal = bootstrap?.Modal?.getOrCreateInstance(pickerModalEl) ?? null;
                dbg('bootstrap modal instance ready');
            } catch (e) {
                dbg('Không thể tạo bootstrap modal instance: ' + e.message, 'error');
                pickerModal = null;
            }

            // base url cho fetch
            const relatedBase = "{{ url('admin/taisan/related') }}"; // => /taisan/related

            const selectedAssets = new Map();

            // attach open button: đảm bảo modal mở dù data-bs có lỗi
            if (openPickerBtn) {
                openPickerBtn.addEventListener('click', (e) => {
                    dbg('Open picker button clicked');
                    if (pickerModal) pickerModal.show();
                    else {
                        // fallback: add .show and backdrop
                        pickerModalEl.classList.add('show');
                        pickerModalEl.style.display = 'block';
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                        document.body.classList.add('modal-open');
                    }
                });
            }

            // render preview function
            function renderPreview() {
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
                dbg('Preview updated: ' + selectedAssets.size + ' items');
            }

            // load when click loai
            if (loaiBtns.length === 0) dbg('Chưa có button loại (loaiBtns.length=0) — kiểm tra $loaiTaiSans trên controller', 'warn');

            loaiBtns.forEach(btn => {
                btn.addEventListener('click', async () => {
                    try {
                        // ui active
                        loaiBtns.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        const loaiId = btn.dataset.id;
                        listTaiSan.innerHTML = '<p class="text-muted p-2">⏳ Đang tải...</p>';
                        dbg('Fetch assets for loaiId=' + loaiId);

                        const res = await fetch(`${relatedBase}/${loaiId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) {
                            dbg('Fetch trả lỗi: ' + res.status + ' ' + res.statusText, 'error');
                            listTaiSan.innerHTML = `<p class="text-danger p-2">Lỗi tải dữ liệu (${res.status})</p>`;
                            return;
                        }
                        const data = await res.json();
                        if (!Array.isArray(data) || data.length === 0) {
                            listTaiSan.innerHTML = '<p class="text-muted p-2">Không có tài sản thuộc loại này.</p>';
                            return;
                        }

                        listTaiSan.innerHTML = '';
                        data.forEach(item => {
                            const div = document.createElement('div');
                            div.className = 'asset-item mb-2';
                            const checked = selectedAssets.has(item.id) ? 'checked' : '';
                            div.innerHTML = `
              <input class="form-check-input" type="checkbox" value="${item.id}" id="ts_${item.id}" ${checked}>
              <img src="${item.hinh_anh || 'https://via.placeholder.com/70'}" class="asset-img" alt="">
              <label class="form-check-label" for="ts_${item.id}">
                <strong>${item.ma_tai_san ?? '---'}</strong> - ${item.ten_tai_san}
                <br><small>${item.tinh_trang ?? 'Không rõ'}</small>
              </label>`;
                            const chk = div.querySelector('input[type="checkbox"]');
                            chk.addEventListener('change', e => {
                                if (e.target.checked) selectedAssets.set(item.id, item);
                                else selectedAssets.delete(item.id);
                                renderPreview();
                            });
                            listTaiSan.appendChild(div);
                        });

                        renderPreview();
                    } catch (err) {
                        dbg('Exception fetch: ' + err.message, 'error');
                        listTaiSan.innerHTML = '<p class="text-danger p-2">Lỗi tải dữ liệu (xem console)</p>';
                    }
                });
            });

            // clear all
            if (clearAllBtn) clearAllBtn.addEventListener('click', () => {
                if (!confirm('Bạn có chắc muốn xóa tất cả tài sản đã chọn không?')) return;
                selectedAssets.clear();
                document.querySelectorAll('#list_taisan input[type=checkbox]').forEach(chk => chk.checked = false);
                renderPreview();
                dbg('All cleared by user');
            });

            // confirm selection
            confirmBtn.addEventListener('click', () => {
                dbg('ConfirmSelection clicked');
                // remove old hidden inputs
                assetForm.querySelectorAll('input[name="tai_san_ids[]"]').forEach(i => i.remove());

                if (selectedAssets.size === 0) {
                    selectedAssetsList.innerHTML = '<span class="text-muted small">Chưa chọn</span>';
                } else {
                    selectedAssetsList.innerHTML = '';
                    selectedAssets.forEach(item => {
                        selectedAssetsList.innerHTML += `<span class="badge bg-success me-1 mb-1">${item.ten_tai_san}</span>`;
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'tai_san_ids[]';
                        hidden.value = item.id;
                        assetForm.appendChild(hidden);
                    });
                }

                // hide modal safely
                if (pickerModal) {
                    try {
                        pickerModal.hide();
                        dbg('pickerModal.hide() called');
                    } catch (e) {
                        dbg('Lỗi khi hide modal: ' + e.message, 'error');
                        // fallback manual
                        pickerModalEl.classList.remove('show');
                        pickerModalEl.style.display = 'none';
                        document.body.classList.remove('modal-open');
                        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    }
                } else {
                    // fallback manual
                    pickerModalEl.classList.remove('show');
                    pickerModalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
                    dbg('Modal closed via fallback');
                }
            });

            dbg('Setup completed');
        }); // DOMContentLoaded
    })();
</script>
@endsection