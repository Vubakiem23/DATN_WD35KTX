@extends('admin.layouts.admin')

@section('title','Quản lý phòng')

@section('content')
    <div class="container-fluid">

        @if(session('status'))
            @push('scripts')
            <script>window.addEventListener('DOMContentLoaded',()=>{(window.showToast||alert)(@json(session('status')),'success')});</script>
            @endpush
            <noscript>
                <div class="alert alert-success">{{ session('status') }}</div>
            </noscript>
        @endif
        @if(session('error'))
            @push('scripts')
            <script>window.addEventListener('DOMContentLoaded',()=>{(window.showToast||alert)(@json(strip_tags(session('error'))),'error')});</script>
            @endpush
            <noscript>
                <div class="alert alert-danger">{!! session('error') !!}</div>
            </noscript>
        @endif

        {{--  --}}

        {{-- Extra prominent create button to ensure visibility --}}


        {{-- Tabs by Khu (building) --}}
        
                <div>
                    <h3 class="khu-page__title mb-1">Khu Ký Túc Xá</h3>
                    <p class="text-muted mb-0">Theo dõi và tổ chức các khu theo giới tính và số lượng phòng.</p>
                    </div>
        @php
            $khuList = $phongs->groupBy(function($p){ return optional($p->khu)->ten_khu ?? 'Không xác định'; });
            $firstKhu = $khuList->keys()->first() ?? '';
        @endphp

        <ul class="nav nav-tabs mb-3" id="khuTabs" role="tablist">
            @foreach($khuList->keys() as $k => $khu)
                @php $slug = \Illuminate\Support\Str::slug($khu) ?: 'khu-'.$k; @endphp
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $khu == $firstKhu ? 'active' : '' }}" id="tab-{{ $slug }}"
                            data-bs-toggle="tab" data-bs-target="#khu-{{ $slug }}"
                            data-toggle="tab" data-target="#khu-{{ $slug }}"
                            type="button" role="tab">{{ $khu ?: 'Không xác định' }}</button>
                </li>
            @endforeach
        </ul>
        <div class="d-flex justify-content-between align-items-center mb-3"> 
                <div>
                    <h3 class="room-page__title mb-1">Danh sách các phòng</h3>
                    <p class="text-muted mb-0">Theo dõi và tổ chức các Phòng theo từng Khu</p>
                </div>
            <div class="d-flex gap-2">
            <a href="{{ route('phong.create') }}" class="btn btn-dergin btn-dergin--info"><i class="fa fa-plus"></i><span>Tạo Phòng</span> </a>
            </div>
        </div>
        <!-- Ô tìm kiếm -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                    placeholder="Tìm theo tên phòng...">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                    <i class="fa fa-filter mr-1"></i> Bộ lọc
                </button>

                @if (!empty(request('search')) || request()->filled('status') || request()->filled('price_min') || request()->filled('price_max'))
                    <a href="{{ route('phong.index') }}" class="btn btn-outline-secondary">Xóa</a>
                @endif
            </div>
        </form>
        @push('styles')
            <style>
                        .khu-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
                        .room-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
            .room-table-wrapper{background:#fff;border-radius:14px;box-shadow:0 10px 30px rgba(15,23,42,0.06);padding:1.25rem}
            .room-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
            .room-table thead th{font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;border:none;padding-bottom:.75rem}
            .room-table tbody tr{background:#f9fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
            .room-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(15,23,42,0.08)}
            .room-table tbody td{border:none;vertical-align:middle;padding:1rem .95rem}
            .room-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
            .room-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
            .room-thumb-cell{width:96px}
            .room-thumb{width:64px;height:64px;border-radius:14px;overflow:hidden;flex:0 0 64px;background:#e9ecef;display:flex;align-items:center;justify-content:center}
            .room-thumb img{width:100%;height:100%;object-fit:cover;cursor:pointer}
            .room-thumb svg{width:32px;height:32px;color:#adb5bd}
            .room-meta{display:flex;flex-direction:column;gap:.25rem}
            .room-meta .title{font-size:1rem;font-weight:600;color:#1f2937}
            .room-meta .sub{font-size:.8rem;color:#6c757d}
            .badge-status{font-size:.72rem;padding:.35rem .6rem;border-radius:999px}
            .btn-dergin{display:inline-flex;align-items:center;justify-content:center;gap:.35rem;padding:.4rem .9rem;border-radius:999px;font-weight:600;font-size:.72rem;border:none;color:#fff;background:linear-gradient(135deg,#4e54c8 0%,#8f94fb 100%);box-shadow:0 6px 16px rgba(78,84,200,.22);transition:transform .2s ease,box-shadow .2s ease}
            .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 10px 22px rgba(78,84,200,.32);color:#fff}
            .btn-dergin i{font-size:.8rem}
            .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
            .btn-dergin--info{background:linear-gradient(135deg,#0ea5e9 0%,#2563eb 100%)}
            .btn-dergin--danger{background:linear-gradient(135deg,#f43f5e 0%,#ef4444 100%)}
            .btn-dergin:disabled,.btn-dergin.disabled{opacity:.5;pointer-events:none}
            .room-actions{display:flex;justify-content:center}
            .room-actions.dropdown{position:relative}
            .room-actions .action-gear{min-width:40px;padding:.45rem .7rem;border-radius:999px}
            .room-actions .dropdown-menu{position:absolute;top:50% !important;right:110%;left:auto;transform:translateY(-50%);z-index:1050;min-width:190px;border-radius:16px;padding:.4rem 0;margin:0;border:1px solid #e5e7eb;box-shadow:0 16px 40px rgba(15,23,42,.18);font-size:.82rem;background:#fff}
            .room-actions .dropdown-item{display:flex;align-items:center;gap:.55rem;padding:.42rem .9rem;color:#4b5563}
            .room-actions .dropdown-item i{width:16px;text-align:center}
            .room-actions .dropdown-item:hover{background:#eef2ff;color:#111827}
            .price-tag{font-weight:600;color:#111827}
            .room-empty-state{font-size:.88rem;color:#6b7280}
            .room-pagination{margin-top:1.25rem}
            .room-pagination__summary{font-size:.78rem;color:#6b7280}
            .room-pagination__nav .pagination{margin-bottom:0}
            @media (max-width:992px){
                .room-table thead{display:none}
                .room-table tbody{display:block}
                .room-table tbody tr{display:flex;flex-direction:column;padding:1rem}
                .room-table tbody td{display:flex;justify-content:space-between;padding:.35rem 0}
                .room-table tbody td[data-label]{position:relative;padding-left:130px}
                .room-table tbody td[data-label]::before{content:attr(data-label);position:absolute;left:0;font-weight:600;color:#6b7280;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}
                .room-actions{justify-content:flex-start;flex-wrap:nowrap;overflow-x:auto;gap:.35rem}
                .room-actions .btn-dergin{flex:0 0 auto}
            }
            </style>
        @endpush
        <div class="tab-content">
            @foreach($khuList as $k => $items)
                @php $khu = optional($items->first()->khu)->ten_khu ?? 'Không xác định'; $slug = \Illuminate\Support\Str::slug($khu) ?: 'khu-'.$k; @endphp
                <div class="tab-pane fade {{ $khu == $firstKhu ? 'show active' : '' }}" id="khu-{{ $slug }}"
                     role="tabpanel" data-page-size="8">
                    <div class="room-table-wrapper">
                        <div class="table-responsive">
                            <table class="table align-middle table-hover room-table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Ảnh</th>
                                        <th>Phòng</th>
                                        <th>Khu</th>
                                        <th>Loại</th>
                                        <th>Giới tính</th>
                                        <th>Sức chứa</th>
                                        <th>Hiện tại</th>
                                        <th>Giá slot</th>
                                        <th>Ghi chú</th>
                                        <th>Trạng thái</th>
                                        <th class="text-end">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                        @foreach($items as $p)
                                    @php
                                        $totalSlots = $p->totalSlots();
                                        $usedSlots = $p->usedSlots();
                                        $available = max(0, $totalSlots - $usedSlots);
                                        $status = 'partial';
                                        if ($totalSlots === 0) { $status = 'no-slot'; }
                                        elseif ($available === 0) { $status = 'full'; }
                                        elseif ($usedSlots === 0) { $status = 'empty'; }
                                        $statusLabel = $p->occupancyLabel();
                                        $statusClass = match(true) {
                                            $status === 'full' => 'bg-danger',
                                            $status === 'empty' => 'bg-success',
                                            $status === 'no-slot' => 'bg-secondary',
                                            default => 'bg-warning text-dark',
                                        };
                                        $slotPrice = optional($p->khu)->gia_moi_slot;
                                        if (is_null($slotPrice) || $slotPrice <= 0) {
                                            $slotPrice = 0;
                                        }
                                    @endphp
                                    <tr class="room-row" data-name="{{ Str::lower($p->ten_phong) }}" data-status="{{ $status }}" data-price="{{ (int) $slotPrice }}">
                                        <td data-label="Ảnh" class="text-center room-thumb-cell">
                                            <div class="room-thumb mx-auto">
                                    @if(!empty($p->hinh_anh))
                                                    <img src="{{ asset('storage/'.$p->hinh_anh) }}" class="previewable" alt="{{ $p->ten_phong }}" data-preview="{{ asset('storage/'.$p->hinh_anh) }}">
                                    @else
                                                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <rect width="24" height="24" rx="6" fill="#e5e7eb"/>
                                                        <path d="M5 16L9.5 11L13 14.5L19 9" stroke="#9ca3af" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        @endif
                                    </div>
                                        </td>
                                        <td data-label="Phòng">
                                            <div class="room-meta">
                                                <div class="title">{{ $p->ten_phong }}</div>
                                                @if(!empty($p->ma_phong))
                                                    <div class="sub">Mã: {{ $p->ma_phong }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td data-label="Khu">{{ optional($p->khu)->ten_khu ?? '-' }}</td>
                                        <td data-label="Loại">{{ \App\Models\Phong::labelLoaiPhongBySlots($p->totalSlots()) }}</td>
                                        <td data-label="Giới tính">{{ $p->gioi_tinh ?? '-' }}</td>
                                        <td data-label="Sức chứa">{{ $totalSlots }} chỗ</td>
                                        <td data-label="Hiện tại">{{ $usedSlots }} / {{ $totalSlots }}</td>
                                        <td data-label="Giá slot">
                                            @if($slotPrice > 0)
                                                <span class="price-tag">{{ number_format($slotPrice, 0, ',', '.') }} VND/slot</span>
                                            @else
                                                <span class="text-muted small">Chưa cài giá slot</span>
                                            @endif
                                        </td>
                                        <td data-label="Ghi chú">{{ $p->ghi_chu ? Str::limit($p->ghi_chu, 80) : '—' }}</td>
                                        <td data-label="Trạng thái">
                                            <span class="badge badge-status {{ $statusClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td data-label="Thao tác" class="text-center">
                                            <div class="room-actions dropdown position-relative">
                                                <button type="button" class="btn btn-dergin btn-dergin--muted action-gear">
                                                    <i class="fa fa-gear"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a href="{{ route('phong.show', $p->id) }}" class="dropdown-item">
                                                            <i class="fa fa-eye text-muted"></i>
                                                            <span>Chi tiết</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('phong.edit', $p) }}" class="dropdown-item">
                                                            <i class="fa fa-pencil text-primary"></i>
                                                            <span>Sửa</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="{{ route('taisan.byPhong', $p->id) }}" class="dropdown-item">
                                                            <i class="fa fa-archive text-info"></i>
                                                            <span>CSVC</span>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button type="button"
                                                            class="dropdown-item text-danger btn-delete-phong"
                                                            data-form-id="delete-phong-{{ $p->id }}"
                                                            data-ten="{{ $p->ten_phong }}"
                                                            data-used="{{ $p->usedSlots() }}"
                                                            data-total="{{ $p->totalSlots() }}"
                                                            data-assets="{{ $p->taiSan()->count() }}"
                                                            {{ ($p->usedSlots() > 0 || $p->taiSan()->count() > 0) ? 'disabled' : '' }}
                                                            title="{{ $p->usedSlots() > 0 ? 'Không thể xóa phòng đang có người ở' : ($p->taiSan()->count() > 0 ? 'Không thể xóa phòng còn tài sản' : '') }}">
                                                            <i class="fa fa-trash"></i>
                                                            <span>Xóa</span>
                                                        </button>
                                                    </li>
                                                </ul>
                                                <form id="delete-phong-{{ $p->id }}" action="{{ route('phong.destroy', $p) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                                </div>
                        <div class="room-empty-state text-center text-muted py-4 d-none">Không có phòng phù hợp.</div>
                        <div class="room-pagination mt-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <p class="room-pagination__summary mb-0 text-muted small"></p>
                            <nav class="room-pagination__nav d-none" aria-label="Phân trang phòng">
                                <ul class="pagination pagination-sm mb-0"></ul>
                            </nav>
                            </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Phân trang hiển thị bằng JavaScript --}}

    </div>

    @push('scripts')
        <script>
            (function(){
                const DEFAULT_PAGE_SIZE = 8;
                let modalEl;
                let imageModalEl;

                function ensureModal(){
                    if(document.getElementById('confirmDeletePhongModal')) return;
                    const tpl = `
<div class="modal fade" id="confirmDeletePhongModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Xác nhận xóa phòng</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p id="confirmDeletePhongText">Bạn có chắc muốn xóa?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="button" class="btn btn-danger" id="confirmDeletePhongBtn">Xóa</button>
      </div>
    </div>
  </div>
</div>`;
                    document.body.insertAdjacentHTML('beforeend', tpl);
                }

                function ensureImageModal(){
                    if(document.getElementById('imagePreviewPhongModal')) return;
                    const tpl = `
<div class="modal fade" id="imagePreviewPhongModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-0">
        <img id="imagePreviewPhongTag" src="" alt="Preview" style="width:100%;height:auto;display:block;">
      </div>
    </div>
  </div>
</div>`;
                    document.body.insertAdjacentHTML('beforeend', tpl);
                }

                function getActivePane(){
                    return document.querySelector('.tab-pane.show.active') || document.querySelector('.tab-pane');
                }

                function updatePanePagination(pane, resetPage){
                    if(!pane) return;
                    const perPage = parseInt(pane.getAttribute('data-page-size') || DEFAULT_PAGE_SIZE, 10) || DEFAULT_PAGE_SIZE;
                    if(resetPage){
                        pane.dataset.page = '1';
                    }
                    let page = parseInt(pane.dataset.page || '1', 10);
                    if(Number.isNaN(page) || page < 1){
                        page = 1;
                    }
                    const matches = Array.from(pane.querySelectorAll('.room-row[data-match="1"]'));
                    const total = matches.length;
                    const totalPages = Math.max(1, Math.ceil(total / perPage));
                    if(page > totalPages){
                        page = totalPages;
                    }
                    if(total === 0){
                        page = 1;
                    }
                    pane.dataset.page = String(page);

                    const rows = Array.from(pane.querySelectorAll('.room-row'));
                    rows.forEach(function(row){
                        row.style.display = row.dataset.match === '1' ? '' : 'none';
                    });
                    matches.forEach(function(row, index){
                        const start = (page - 1) * perPage;
                        const end = start + perPage;
                        row.style.display = (index >= start && index < end) ? '' : 'none';
                    });

                    const emptyEl = pane.querySelector('.room-empty-state');
                    if(emptyEl){
                        if(total === 0){
                            emptyEl.classList.remove('d-none');
                        } else {
                            emptyEl.classList.add('d-none');
                        }
                    }

                    const summaryEl = pane.querySelector('.room-pagination__summary');
                    if(summaryEl){
                        if(total === 0){
                            summaryEl.textContent = '';
                        } else {
                            const start = (page - 1) * perPage + 1;
                            const end = Math.min(start + perPage - 1, total);
                            summaryEl.textContent = `Hiển thị ${start}-${end} trên ${total} phòng`;
                        }
                    }

                    const paginationWrapper = pane.querySelector('.room-pagination');
                    if(paginationWrapper){
                        if(total === 0){
                            paginationWrapper.classList.add('d-none');
                        } else {
                            paginationWrapper.classList.remove('d-none');
                        }
                    }

                    const navWrapper = pane.querySelector('.room-pagination__nav');
                    const paginationList = navWrapper ? navWrapper.querySelector('.pagination') : null;
                    if(navWrapper && paginationList){
                        paginationList.innerHTML = '';
                        if(total <= perPage || total === 0){
                            navWrapper.classList.add('d-none');
                            return;
                        }
                        navWrapper.classList.remove('d-none');

                        const createItem = function(label, targetPage, disabled, active){
                            const li = document.createElement('li');
                            li.className = 'page-item';
                            if(disabled) li.classList.add('disabled');
                            if(active) li.classList.add('active');
                            const a = document.createElement('a');
                            a.className = 'page-link';
                            a.href = '#';
                            a.dataset.page = targetPage;
                            a.textContent = label;
                            li.appendChild(a);
                            paginationList.appendChild(li);
                        };

                        createItem('‹', page - 1, page === 1, false);
                        let startPage = Math.max(1, page - 2);
                        let endPage = Math.min(totalPages, startPage + 4);
                        startPage = Math.max(1, endPage - 4);
                        for(let p = startPage; p <= endPage; p++){
                            createItem(String(p), p, false, p === page);
                        }
                        createItem('›', page + 1, page === totalPages, false);
                    }
                }

                function applyRoomFilter(resetPage = true){
                    const activePane = getActivePane();
                    if(!activePane) return;
                    const term = (document.getElementById('roomFilterName')?.value || '').toLowerCase();
                    const st = (document.getElementById('roomFilterStatus')?.value || 'all');
                    const min = parseInt(document.getElementById('roomFilterPriceMin')?.value || '');
                    const max = parseInt(document.getElementById('roomFilterPriceMax')?.value || '');
                    const rows = Array.from(activePane.querySelectorAll('.room-row'));
                    rows.forEach(function(row){
                        const name = (row.getAttribute('data-name')||'').toLowerCase();
                        const status = row.getAttribute('data-status')||'';
                        const price = parseInt(row.getAttribute('data-price')||'0');
                        const okName = !term || name.indexOf(term) !== -1;
                        const okStatus = (st==='all') || (status===st);
                        const okPrice = (isNaN(min) || price>=min) && (isNaN(max) || price<=max);
                        row.dataset.match = (okName && okStatus && okPrice) ? '1' : '0';
                    });
                    updatePanePagination(activePane, resetPage);
                }

                function initPagination(){
                    document.querySelectorAll('.tab-pane').forEach(function(pane){
                        if(!pane.dataset.page){
                            pane.dataset.page = '1';
                        }
                        if(!pane.getAttribute('data-page-size')){
                            pane.setAttribute('data-page-size', DEFAULT_PAGE_SIZE);
                        }
                        pane.querySelectorAll('.room-row').forEach(function(row){
                            if(!row.dataset.match){
                                row.dataset.match = '1';
                            }
                        });
                    });
                    applyRoomFilter(true);
                }

                if(document.readyState === 'loading'){
                    document.addEventListener('DOMContentLoaded', initPagination);
                } else {
                    initPagination();
                }

                document.addEventListener('click', function(e){
                    const allMenus = document.querySelectorAll('.room-actions .dropdown-menu');
                    const gearBtn = e.target.closest('.action-gear');
                    const insideMenu = e.target.closest('.room-actions .dropdown-menu');

                    if(gearBtn){
                        e.preventDefault();
                        const wrapper = gearBtn.closest('.room-actions');
                        const menu = wrapper ? wrapper.querySelector('.dropdown-menu') : null;
                        const isOpen = menu && menu.classList.contains('show');
                        allMenus.forEach(function(m){ m.classList.remove('show'); });
                        if(menu && !isOpen){
                            menu.classList.add('show');
                        }
                        return;
                    }

                    if(!insideMenu){
                        allMenus.forEach(function(m){ m.classList.remove('show'); });
                    } else if(e.target.closest('.dropdown-item') || e.target.closest('button')){
                        allMenus.forEach(function(m){ m.classList.remove('show'); });
                    }

                    const btn = e.target.closest('.btn-delete-phong');
                    if(btn){
                    const disabled = btn.hasAttribute('disabled');
                    if(disabled) return;
                    const formId = btn.getAttribute('data-form-id');
                    const ten = btn.getAttribute('data-ten') || 'phòng';
                    const used = parseInt(btn.getAttribute('data-used')||'0',10);
                    const total = parseInt(btn.getAttribute('data-total')||'0',10);

                        if(window.bootstrap && typeof window.bootstrap.Modal === 'function'){
                        ensureModal();
                        modalEl = document.getElementById('confirmDeletePhongModal');
                        const msgEl = document.getElementById('confirmDeletePhongText');
                        msgEl.textContent = `Bạn chuẩn bị xóa "${ten}". Thao tác không thể hoàn tác. Hiện tại: ${used}/${total}. Bạn có chắc?`;
                        const bsModal = new bootstrap.Modal(modalEl);
                        const confirmBtn = document.getElementById('confirmDeletePhongBtn');
                        confirmBtn.onclick = function(){
                            const form = document.getElementById(formId);
                            if(form) form.submit();
                            bsModal.hide();
                        };
                        bsModal.show();
                    } else {
                        const ok = window.confirm(`Bạn chuẩn bị xóa "${ten}". Thao tác không thể hoàn tác. Hiện tại: ${used}/${total}. Bạn có chắc?`);
                            if(ok){
                            const form = document.getElementById(formId);
                            if(form) form.submit();
                            }
                        }
                        return;
                    }

                    const img = e.target.closest('img.previewable');
                    if(img){
                    const src = img.getAttribute('data-preview') || img.getAttribute('src');
                        if(!src) return;
                    ensureImageModal();
                    imageModalEl = document.getElementById('imagePreviewPhongModal');
                    const tag = document.getElementById('imagePreviewPhongTag');
                    tag.src = src;
                        if(window.bootstrap && typeof window.bootstrap.Modal === 'function'){
                        const bsModal = new bootstrap.Modal(imageModalEl);
                        bsModal.show();
                    } else {
                        window.open(src, '_blank');
                    }
                        return;
                    }

                    const pageLink = e.target.closest('.room-pagination .page-link');
                    if(pageLink){
                        e.preventDefault();
                        const li = pageLink.parentElement;
                        if(li && (li.classList.contains('disabled') || li.classList.contains('active'))){
                            return;
                        }
                        const targetPage = parseInt(pageLink.dataset.page, 10);
                        if(!targetPage) return;
                        const pane = pageLink.closest('.tab-pane');
                        if(!pane) return;
                        pane.dataset.page = String(targetPage);
                        updatePanePagination(pane, false);
                        const wrapper = pane.querySelector('.room-table-wrapper');
                        if(wrapper){
                            wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                        return;
                    }
                });

                const nameInput = document.getElementById('roomFilterName');
                if(nameInput) nameInput.addEventListener('input', function(){ applyRoomFilter(true); });
                const statusSelect = document.getElementById('roomFilterStatus');
                if(statusSelect) statusSelect.addEventListener('change', function(){ applyRoomFilter(true); });
                const minInput = document.getElementById('roomFilterPriceMin');
                if(minInput) minInput.addEventListener('input', function(){ applyRoomFilter(true); });
                const maxInput = document.getElementById('roomFilterPriceMax');
                if(maxInput) maxInput.addEventListener('input', function(){ applyRoomFilter(true); });

                document.querySelectorAll('#khuTabs button').forEach(function(btn){
                    btn.addEventListener('click', function(){
                        setTimeout(function(){ applyRoomFilter(true); }, 0);
                    });
                });
            })();
            function openAddGuest(phongId) {
                const url = '/sinhvien/create?phong_id=' + phongId;
                window.location.href = url;
            }

            // Ensure tabs activate across Bootstrap versions (fallback)
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('#khuTabs button').forEach(function (btn) {
                    btn.addEventListener('click', function (e) {
                        var target = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
                        if (!target) return;
                        // hide other panes
                        document.querySelectorAll('.tab-pane').forEach(function (p) {
                            p.classList.remove('show', 'active');
                        });
                        var pane = document.querySelector(target);
                        if (pane) {
                            pane.classList.add('show', 'active');
                        }
                        document.querySelectorAll('#khuTabs button').forEach(function (b) {
                            b.classList.remove('active');
                        });
                        btn.classList.add('active');
                    });
                });
            });
        </script>
    @endpush

    {{-- MODAL BỘ LỌC --}}
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Bộ lọc phòng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>

                <form method="GET" id="filterForm">
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="small text-muted">Tên phòng</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control" placeholder="Tìm theo tên phòng...">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="small text-muted">Trạng thái</label>
                                    <select name="status" class="form-control">
                                        <option value="">-- Tất cả --</option>
                                        <option value="empty" @selected(request('status') == 'empty')>Trống</option>
                                        <option value="partial" @selected(request('status') == 'partial')>Còn chỗ</option>
                                        <option value="full" @selected(request('status') == 'full')>Đã ở full</option>
                                        <option value="no-slot" @selected(request('status') == 'no-slot')>Chưa có slot</option>
                                    </select>
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="small text-muted">Giá slot từ</label>
                                    <input type="number" min="0" step="1000" name="price_min" 
                                        value="{{ request('price_min') }}" class="form-control" placeholder="0">
                                </div>

                                <div class="col-md-2 mb-3">
                                    <label class="small text-muted">Giá slot đến</label>
                                    <input type="number" min="0" step="1000" name="price_max" 
                                        value="{{ request('price_max') }}" class="form-control" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <a href="{{ route('phong.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                        <button type="submit" class="btn btn-primary">Áp dụng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Mở modal bộ lọc phòng (chạy được cho cả Bootstrap 4 và 5)
            (function() {
                document.addEventListener('DOMContentLoaded', function() {
                    var btn = document.getElementById('openFilterModalBtn');
                    if (!btn) return;

                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        var modalEl = document.getElementById('filterModal');
                        if (!modalEl) return;

                        try {
                            if (window.bootstrap && bootstrap.Modal) {
                                var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                                modal.show();
                            } else if (window.$ && $('#filterModal').modal) {
                                $('#filterModal').modal('show');
                            }
                        } catch (err) {
                            if (window.$ && $('#filterModal').modal) {
                                $('#filterModal').modal('show');
                            }
                        }
                    });
                });
            })();
        </script>
    @endpush

@endsection
