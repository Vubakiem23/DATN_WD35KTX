@extends('admin.layouts.admin')

@section('title', 'Thêm tài sản vào phòng')

@section('content')
<style>
    .card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    }

    .page-title {
        font-weight: 700;
        color: #1e293b;
    }

    .form-select,
    .form-control {
        border-radius: 10px;
        transition: all 0.2s;
    }

    .form-select:focus,
    .form-control:focus {
        box-shadow: 0 0 6px rgba(25, 135, 84, 0.4);
        border-color: #198754;
    }

    #list_taisan {
        max-height: 420px;
        overflow-y: auto;
    }

    .asset-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        transition: all 0.2s ease;
    }

    .asset-item:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .asset-img {
        width: 70px;
        height: 70px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .asset-info {
        flex: 1 1 auto;
        min-width: 0;
    }

    .asset-info strong {
        color: #0f172a;
    }

    .asset-info small {
        color: #64748b;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: #198754;
    }

    .form-check-label {
        cursor: pointer;
    }
    .asset-actions{
        margin-left: auto;
        display: inline-flex;
        align-items: center;
        flex: 0 0 auto;
    }
    .asset-actions .btn{
        white-space: nowrap;
    }
</style>

<div class="container mt-4">

        <div>
            <h3 class="page-title mb-1">🧰 Thêm tài sản vào phòng</h3>
            <p class="text-muted small mb-0">Chọn loại tài sản → chọn từng tài sản → điền thông tin → lưu.</p>
        </div>
        <a href="{{ route('taisan.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="fa fa-arrow-left me-1"></i> Quay lại
        </a>


    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- 🧾 Form thêm tài sản --}}
    <form id="assetForm" action="{{ route('taisan.store') }}" method="POST" class="card p-4 shadow-sm">
        @csrf
        <h5 class="fw-semibold text-success mb-3">Thông tin gán tài sản vào phòng</h5>

        {{-- Chọn loại tài sản --}}
        <div class="mb-3">
            <label class="form-label fw-semibold text-secondary">Loại tài sản</label>
            <select id="loai_id" class="form-select">
                <option value="">-- Chọn loại tài sản --</option>
                @foreach($loaiTaiSans as $loai)
                <option value="{{ $loai->id }}">{{ $loai->ten_loai }}</option>
                @endforeach
            </select>
        </div>

        {{-- Danh sách tài sản --}}
        <div class="mt-3" id="taisan_section" style="display:none;">
            <label class="form-label fw-semibold text-secondary mb-2">Danh sách tài sản trong kho</label>
            <div id="list_taisan" class="border p-3 bg-white rounded">
                <p class="text-muted mb-0">Vui lòng chọn loại tài sản để xem danh sách...</p>
            </div>
        </div>

        {{-- Thông tin phòng và tình trạng --}}
        <div class="row g-3 mt-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary">Phòng</label>
                <select name="phong_id" class="form-select" required>
                    <option value="">-- Chọn phòng --</option>
                    @foreach($phongs as $phong)
                    <option value="{{ $phong->id }}">{{ $phong->ten_phong }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold text-secondary">Tình trạng khi gán</label>
                <select name="tinh_trang" class="form-select" required>
                    <option value="Bình thường">Bình thường</option>
                    <option value="Hỏng">Hỏng</option>
                    <option value="Cần bảo trì">Cần bảo trì</option>
                </select>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-success px-4 py-2 rounded-pill shadow-sm">
                💾 Lưu tài sản vào phòng
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loaiSelect = document.getElementById('loai_id');
    const listTaiSan = document.getElementById('list_taisan');
    const taisanSection = document.getElementById('taisan_section');

    loaiSelect.addEventListener('change', async function() {
        const loaiId = this.value;
        taisanSection.style.display = 'block';
        listTaiSan.innerHTML = '<p class="text-muted">Đang tải danh sách tài sản...</p>';

        if (!loaiId) {
            listTaiSan.innerHTML = '<p class="text-muted">Vui lòng chọn loại tài sản.</p>';
            return;
        }

        const res = await fetch(`{{ route('taisan.related', '') }}/${loaiId}`);
        if (!res.ok) {
            listTaiSan.innerHTML = '<p class="text-danger">Không thể tải danh sách.</p>';
            return;
        }

        const data = await res.json();
        if (data.length === 0) {
            listTaiSan.innerHTML = '<p class="text-danger">Không có tài sản nào trong kho thuộc loại này.</p>';
            return;
        }

        listTaiSan.innerHTML = '';
        data.forEach(item => {
            const imageUrl = item.hinh_anh || `https://via.placeholder.com/70x70?text=No+Image`;
            const wrapper = document.createElement('div');
            wrapper.className = 'asset-item mb-3';
            wrapper.innerHTML = `
                <img src="${imageUrl}" alt="${item.ten_tai_san}" class="asset-img">
                <div class="asset-info">
                    <div class="form-check-label">
                        <strong>${item.ma_tai_san ?? '---'}</strong> - ${item.ten_tai_san}
                        <br>
                        <small>Tình trạng: ${item.tinh_trang ?? 'Không rõ'}</small>
                    </div>
                </div>
                <div class="asset-actions">
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" data-role="pick-btn" data-id="${item.id}">
                        Chọn
                    </button>
                </div>
            `;
            listTaiSan.appendChild(wrapper);

            const pickBtn = wrapper.querySelector('[data-role="pick-btn"]');
            const getHiddenInput = () => wrapper.querySelector('input[type="hidden"][data-role="selected-id"]');

            const syncButtonState = (selected) => {
                if (selected) {
                    pickBtn.textContent = 'Đã chọn';
                    pickBtn.classList.remove('btn-outline-primary');
                    pickBtn.classList.add('btn-primary');
                } else {
                    pickBtn.textContent = 'Chọn';
                    pickBtn.classList.add('btn-outline-primary');
                    pickBtn.classList.remove('btn-primary');
                }
            };

            const setSelected = (selected) => {
                wrapper.dataset.selected = selected ? '1' : '';
                const exists = getHiddenInput();
                if (selected && !exists) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'tai_san_ids[]';
                    hidden.value = String(item.id);
                    hidden.setAttribute('data-role', 'selected-id');
                    wrapper.appendChild(hidden);
                } else if (!selected && exists) {
                    exists.remove();
                }
                syncButtonState(selected);
            };

            pickBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const isSelected = wrapper.dataset.selected === '1';
                setSelected(!isSelected);
            });
            wrapper.addEventListener('click', (e) => {
                if (e.target.closest('[data-role="pick-btn"]')) return;
                const isSelected = wrapper.dataset.selected === '1';
                setSelected(!isSelected);
            });
            setSelected(false);
        });
    });
});
</script>
@endsection
