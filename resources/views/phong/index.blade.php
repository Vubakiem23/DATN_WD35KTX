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
        <h4>Khu</h4>
        @php
            $khuList = $phongs->groupBy('khu');
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
            <h4>Danh sách các phòng</h4>
            <a href="{{ route('phong.create') }}" class="btn btn-success">Tạo phòng</a>
        </div>
        <div class="tab-content">
            @foreach($khuList as $k => $items)
                @php $khu = $items->first()->khu ?? ''; $slug = \Illuminate\Support\Str::slug($khu) ?: 'khu-'.$k; @endphp
                <div class="tab-pane fade {{ $khu == $firstKhu ? 'show active' : '' }}" id="khu-{{ $slug }}"
                     role="tabpanel">
                    <div class="row g-3">
                        @foreach($items as $p)
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <strong>{{ $p->ten_phong }}</strong>
                                        <span
                                            class="badge bg-{{ $p->availableSlots() == 0 && $p->totalSlots() > 0 ? 'warning text-dark' : 'success' }}">{{ $p->occupancyLabel() }}</span>
                                    </div>
                                    @if(!empty($p->hinh_anh))
                                        <img src="{{ asset('storage/'.$p->hinh_anh) }}" class="card-img-top previewable"
                                             style="height:220px;object-fit:cover;cursor:pointer" alt="{{ $p->ten_phong }}"
                                             data-preview="{{ asset('storage/'.$p->hinh_anh) }}">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center"
                                             style="height:220px;background:#f8f9fa">
                                            {{-- inline SVG placeholder so image always shows even if no file --}}
                                            <svg width="80" height="60" viewBox="0 0 24 24" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <rect width="24" height="24" rx="2" fill="#e9ecef"/>
                                                <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                                                      stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <p class="mb-1"><strong>Khu:</strong> {{ $p->khu ?? '-' }}</p>
                                        <p class="mb-1"><strong>Loại:</strong> {{ \App\Models\Phong::labelLoaiPhongBySlots($p->totalSlots()) }}</p>
                                        <p class="mb-1"><strong>Giới tính:</strong> {{ $p->gioi_tinh ?? '-' }}</p>
                                        <p class="mb-1"><strong>Sức chứa:</strong> {{ $p->totalSlots() }} chỗ</p>
                                        <p class="mb-1"><strong>Hiện tại:</strong> {{ $p->usedSlots() }}
                                            / {{ $p->totalSlots() }}</p>
                                        @if(!is_null($p->gia_phong))
                                            <p class="mb-1"><strong>Giá phòng:</strong> {{ number_format($p->gia_phong, 0, ',', '.') }} VND/tháng</p>
                                        @endif
                                        @if($p->ghi_chu)
                                            <p class="text-muted small">{{ Str::limit($p->ghi_chu, 120) }}</p>
                                        @endif
                                    </div>
                                    <div class="card-footer d-flex gap-2">
                                        <a href="{{ route('phong.edit', $p) }}" class="btn btn-sm btn-info flex-fill">Chỉnh
                                            sửa</a>
                                        <a href="{{ route('phong.show', $p->id) }}"
                                           class="btn btn-sm btn-secondary flex-fill">Thông Tin</a>

                                        <a href="{{ route('taisan.index') }}?phong_id={{ $p->id }}"
                                           class="btn btn-sm btn-primary flex-fill">Tài Sản</a>
                                        <form id="delete-phong-{{ $p->id }}" action="{{ route('phong.destroy', $p) }}" method="POST" style="display:inline">
                                            @csrf @method('DELETE')
                                            <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-phong"
                                                    data-form-id="delete-phong-{{ $p->id }}"
                                                    data-ten="{{ $p->ten_phong }}"
                                                    data-used="{{ $p->usedSlots() }}"
                                                    data-total="{{ $p->totalSlots() }}"
                                                    data-assets="{{ $p->taiSan()->count() }}"
                                                    {{ ($p->usedSlots() > 0 || $p->taiSan()->count() > 0) ? 'disabled' : '' }}
                                                    title="{{ $p->usedSlots() > 0 ? 'Không thể xóa phòng đang có người ở' : ($p->taiSan()->count() > 0 ? 'Không thể xóa phòng còn tài sản' : '') }}">
                                                Xóa
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bỏ phân trang theo yêu cầu --}}

    </div>

    @push('scripts')
        <script>
            // Delete confirmation modal logic
            (function(){
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
                document.addEventListener('click', function(e){
                    const btn = e.target.closest('.btn-delete-phong');
                    if(!btn) return;
                    const disabled = btn.hasAttribute('disabled');
                    if(disabled) return;
                    const formId = btn.getAttribute('data-form-id');
                    const ten = btn.getAttribute('data-ten') || 'phòng';
                    const used = parseInt(btn.getAttribute('data-used')||'0',10);
                    const total = parseInt(btn.getAttribute('data-total')||'0',10);

                    // Nếu có Bootstrap thì dùng modal, nếu không thì confirm()
                    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
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
                        if (ok) {
                            const form = document.getElementById(formId);
                            if(form) form.submit();
                        }
                    }
                });
                // Preview image when clicking on room image
                document.addEventListener('click', function(e){
                    const img = e.target.closest('img.previewable');
                    if(!img) return;
                    const src = img.getAttribute('data-preview') || img.getAttribute('src');
                    if (!src) return;
                    ensureImageModal();
                    imageModalEl = document.getElementById('imagePreviewPhongModal');
                    const tag = document.getElementById('imagePreviewPhongTag');
                    tag.src = src;
                    if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                        const bsModal = new bootstrap.Modal(imageModalEl);
                        bsModal.show();
                    } else {
                        window.open(src, '_blank');
                    }
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

@endsection
