@extends('admin.layouts.admin')

@section('title','Chi tiết phòng')

@section('content')
<div class="container-fluid khu-detail-page">
  <div class="align-items-center mb-3">
    <div>
      <h3 class="khu-page__title mb-1">Chi Tiết Phòng: {{ $phong->ten_phong }}</h3>
      <p class="text-muted mb-0">Theo dõi và tổ chức Chi Tiết Phòng</p>
    </div>
    <div>
      <a href="{{ route('phong.index') }}" class="btn btn-dergin btn-dergin--muted" title="Quay lại"><i class="fa fa-arrow-left"></i><span>Về danh sách</span></a>
    </div>
  </div>
  @push('styles')
  <style>
    /* Trang trí riêng cho trang chi tiết phòng */
    .khu-page__title{font-size:1.75rem;font-weight:700;color:#1f2937;}
    .khu-detail-page{padding-bottom:2rem}

    /* Buttons + shared styles (match Khu detail) */
    .btn-dergin{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:999px;font-weight:600;font-size:.78rem;border:none;color:#fff;background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%);box-shadow:0 8px 20px rgba(79,70,229,.25);text-decoration:none}
    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(79,70,229,.35);color:#fff}
    .btn-dergin i{font-size:.85rem}
    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
    .btn-dergin--info{background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%)}
    .btn-dergin--danger{background:linear-gradient(135deg,#ef4444 0%,#dc2626 100%)}

    /* Toolbar + table wrapper (match Khu detail) */
    .khu-toolbar{background:#ffffff;border-radius:14px;padding:1rem 1.25rem;box-shadow:0 10px 24px rgba(15,23,42,0.06)}
    .khu-rooms-wrapper{background:#fff;border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,0.08);padding:1.25rem}
    .khu-rooms-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
    .khu-rooms-table thead th{border:none;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;padding-bottom:.75rem}
    .khu-rooms-table tbody tr{background:#f8fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
    .khu-rooms-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(15,23,42,0.12)}
    .khu-rooms-table tbody td{border:none;vertical-align:middle;padding:1rem 1rem}
    .khu-rooms-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
    .khu-rooms-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
    .slot-actions{display:flex;flex-wrap:wrap;gap:.5rem;justify-content:flex-end}

    .room-cover{aspect-ratio: 4 / 3; width:100%; object-fit:cover; border-radius:.25rem;}
    .slot-thumb{width:110px;height:72px;object-fit:cover;border-radius:.35rem;}
    .table td, .table th{vertical-align: middle;}
    .slots-table{table-layout:fixed;width:100%;}
    .slots-table th,.slots-table td{white-space:normal;word-break:break-word;}
    .slots-table th:nth-child(1){width:16%}
    .slots-table th:nth-child(2){width:18%}
    .slots-table th:nth-child(3){width:24%}
    .slots-table th:nth-child(4){width:20%}
    .slots-table th:nth-child(5){width:14%}
    .slots-table th:nth-child(6){width:20%}
    .slot-actions{display:flex;flex-wrap:wrap;gap:.4rem;justify-content:flex-end}
    .slot-actions .btn{padding:.35rem .6rem;font-size:13px}
    .table td .text-trunc{display:inline-block;max-width:100%;white-space:normal;overflow:visible;text-overflow:clip;}
    #assignAssetsModal .modal-dialog{max-width:960px}
    #assignAssetsModal .modal-body{padding:1.75rem}
    #assignAssetsModal .asset-modal{display:flex;flex-direction:column;gap:1.5rem}
    #assignAssetsModal .asset-modal__column{display:flex;flex-direction:column;gap:1rem;background:#fff;border-radius:18px;border:1px solid #e5e7eb;padding:1.35rem 1.5rem;box-shadow:0 16px 36px rgba(15,23,42,.08)}
    #assignAssetsModal .asset-modal__heading{font-weight:600;font-size:1rem;margin-bottom:0;color:#1e1b4b}
    #assignAssetsModal .asset-modal__search .form-control{border-radius:12px;padding:.55rem .95rem;border:1px solid rgba(99,102,241,.2)}
    #assignAssetsModal .asset-modal__search .form-control:focus{border-color:#4f46e5;box-shadow:0 0 0 .2rem rgba(79,70,229,.15)}
    #assignAssetsModal .asset-option-list{display:flex;flex-direction:column;gap:1rem;max-height:360px;overflow-y:auto;padding-right:.4rem}
    #assignAssetsModal .asset-option-list::-webkit-scrollbar{width:6px}
    #assignAssetsModal .asset-option-list::-webkit-scrollbar-thumb{background:#cbd5f5;border-radius:999px}
    #assignAssetsModal .asset-option{border:1px solid #e5e7eb;border-radius:16px;padding:1rem 1.1rem;background:#fff;transition:box-shadow .2s ease,border-color .2s ease;cursor:pointer}
    #assignAssetsModal .asset-option:hover{box-shadow:0 14px 28px rgba(15,23,42,.1);border-color:#c7d2fe}
    #assignAssetsModal .asset-option.is-selected{border-color:#6366f1;box-shadow:0 18px 32px rgba(99,102,241,.22)}
    #assignAssetsModal .asset-option.is-disabled{opacity:.55;pointer-events:none}
    #assignAssetsModal .asset-option__body{display:flex;align-items:center;gap:1rem}
    #assignAssetsModal .asset-option__thumb{width:64px;height:64px;border-radius:16px;overflow:hidden;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:inset 0 0 0 1px rgba(148,163,184,.2)}
    #assignAssetsModal .asset-option__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .asset-option__details{flex:1;display:flex;flex-direction:column;gap:.35rem}
    #assignAssetsModal .asset-option__title{font-weight:600;color:#1e1b4b}
    #assignAssetsModal .asset-option__meta,
    #assignAssetsModal .asset-option__condition{font-size:.85rem;color:#64748b}
    #assignAssetsModal .asset-option__actions{display:flex;flex-direction:column;align-items:flex-end;gap:.35rem}
    #assignAssetsModal .asset-option__actions .btn{border-radius:10px;font-weight:600;padding:.35rem .85rem}
    #assignAssetsModal .selected-assets{display:flex;flex-direction:column;gap:.75rem}
    #assignAssetsModal .selected-asset{border:1px solid #dbeafe;border-radius:16px;padding:1rem 1.1rem;background:#f8fafc;display:flex;flex-direction:column;gap:.75rem;box-shadow:0 14px 28px rgba(15,23,42,.06)}
    #assignAssetsModal .selected-asset__top{display:flex;gap:1rem;align-items:center}
    #assignAssetsModal .selected-asset__thumb{width:56px;height:56px;border-radius:14px;overflow:hidden;flex-shrink:0;background:#fff;box-shadow:inset 0 0 0 1px rgba(148,163,184,.18)}
    #assignAssetsModal .selected-asset__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .selected-asset__info{flex:1;display:flex;flex-direction:column;gap:.35rem}
    #assignAssetsModal .selected-asset__title{font-weight:600;color:#1f2937}
    #assignAssetsModal .selected-asset__meta,
    #assignAssetsModal .selected-asset__condition{font-size:.85rem;color:#64748b}
    #assignAssetsModal .selected-asset__actions{margin-left:auto}
    #assignAssetsModal .selected-asset__actions .btn{border-radius:10px}
    #assignAssetsModal .asset-empty-message{padding:1.5rem 0;text-align:center;color:#94a3b8;font-size:.95rem}
    #assignAssetsModal .slot-current-assets{border:1px solid rgba(79,70,229,.18);border-radius:18px;padding:1rem 1.25rem;background:rgba(79,70,229,.05);display:flex;flex-direction:column;gap:.75rem}
    #assignAssetsModal .slot-current-assets__title{font-weight:600;color:#3730a3}
    #assignAssetsModal .slot-current-assets__list{display:flex;flex-direction:column;gap:.65rem}
    #assignAssetsModal .slot-current-asset{display:flex;gap:.75rem;align-items:center;background:#fff;border-radius:14px;padding:.55rem .8rem;border:1px solid #c7d2fe;box-shadow:0 8px 18px rgba(79,70,229,.15)}
    #assignAssetsModal .slot-current-asset__thumb{width:44px;height:44px;border-radius:12px;overflow:hidden;background:#eef2ff;flex-shrink:0}
    #assignAssetsModal .slot-current-asset__thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .slot-current-asset__info{display:flex;flex-direction:column;gap:.2rem;font-size:.85rem}
    #assignAssetsModal .slot-current-asset__name{font-weight:600;color:#1e1b4b}
    #assignAssetsModal .slot-current-asset__meta{color:#64748b}
    #assignAssetsModal .slot-current-asset__badge{font-size:.7rem;border-radius:999px;padding:.15rem .5rem;background:#ede9fe;color:#5b21b6;margin-left:.35rem}
    @media (max-width: 991.98px){#assignAssetsModal .asset-option__body{flex-direction:column;align-items:flex-start}#assignAssetsModal .asset-option__actions{width:100%;align-items:flex-end}}
    @media (max-width: 575.98px){#assignAssetsModal .modal-body{padding:1.25rem}#assignAssetsModal .asset-option__body{align-items:flex-start}#assignAssetsModal .selected-asset__top{flex-direction:column;align-items:flex-start}}
    #assignStudentModal .modal-dialog{max-width:520px}
    #assignStudentModal .form-group{display:flex;flex-direction:column;gap:.5rem}
    #assignStudentModal .form-group label{font-weight:600;color:#4338ca;margin-bottom:0;letter-spacing:.01em}
    #assignStudentModal .assign-student-select{padding:.65rem 1rem;border-radius:14px;border:1px solid rgba(99,102,241,.28);box-shadow:0 10px 28px rgba(79,70,229,.12);font-weight:600;color:#1e1b4b;transition:border-color .2s ease,box-shadow .2s ease;font-size:14px;line-height:1.5;min-height:calc(2.5rem + 2px)}
    #assignStudentModal .assign-student-select:focus{border-color:#4f46e5;box-shadow:0 0 0 .25rem rgba(79,70,229,.18);outline:none}
    #assignStudentModal .assign-student-select option{white-space:normal;line-height:1.5;padding:.35rem .5rem;font-weight:500}
    @media (max-width: 992px){
      .slots-table th:nth-child(1){width:20%}
      .slots-table th:nth-child(2){width:25%}
      .slots-table th:nth-child(3){width:25%}
      .slots-table th:nth-child(4){width:20%}
      .slots-table th:nth-child(5){width:10%}
      .slots-table th:nth-child(6){width:20%}
    }
  </style>
  @endpush
  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card mb-3 shadow-sm h-100">
    @if($phong->hinh_anh)
      <img src="{{ asset('storage/'.$phong->hinh_anh) }}" class="room-cover" alt="{{ $phong->ten_phong }}">
    @endif
    <div class="card-body">
      <div class="d-flex flex-wrap gap-3 mb-3">
        <span class="badge bg-primary">Khu: {{ optional($phong->khu)->ten_khu ?? '-' }}</span>
        <span class="badge bg-info text-dark">Loại: {{ \App\Models\Phong::labelLoaiPhongBySlots($phong->totalSlots()) }}</span>
        <span class="badge bg-secondary">Sức chứa: {{ $phong->totalSlots() }}</span>
      </div>
        @if(!is_null($phong->gia_phong))
        <div class="mb-3">
          <span class="badge bg-dark">Giá: {{ number_format($phong->gia_phong, 0, ',', '.') }} VND/tháng</span>
        </div>
        @endif
      @php $total=$phong->totalSlots(); $used=$phong->usedSlots(); $pct=$total?round($used*100/$total):0; @endphp
      <div class="small text-muted mb-2">Tỉ lệ lấp đầy: {{ $used }} / {{ $total }} ({{ $pct }}%)</div>
      <div class="progress mb-3" style="height:10px;max-width:420px;">
        <div class="progress-bar {{ $pct==100 ? 'bg-warning text-dark' : 'bg-success' }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      @if($phong->ghi_chu)
        <ul class="list-unstyled mb-0 mt-2 small text-muted">
          <li><i class="fa fa-sticky-note-o me-1"></i>{{ $phong->ghi_chu }}</li>
        </ul>
      @endif
    </div>
      </div>
    </div>
    <div class="col-12 col-lg-8">
      {{-- Danh sách slot --}}
      <div class="khu-toolbar mb-3">
        <div class="d-flex justify-content-end">
          <a href="#" class="btn btn-dergin btn-dergin--info" onclick="openCreateSlots()"><i class="fa fa-plus"></i><span>Tạo slots</span></a>
        </div>
      </div>
      <div class="khu-rooms-wrapper">
          <table class="table align-middle khu-rooms-table">
            <thead>
              <tr>
                <th>Mã slot</th>
                <th>Sinh viên</th>
                <th>CSVC (bàn giao)</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($phong->slots as $slot)
              <tr class="slot-row {{ $slot->sinh_vien_id ? 'occupied' : 'empty' }}">
                <td data-label="Mã slot">{{ $slot->ma_slot }}</td>
                <td data-label="Sinh viên">{{ $slot->sinhVien->ho_ten ?? '-' }}</td>
                <td data-label="CSVC (bàn giao)">
                  @if(($slot->taiSans ?? collect())->count() > 0)
                    <style>
                      .chip{display:inline-flex;align-items:center;gap:.35rem;border:1px solid #e9ecef;border-radius:999px;padding:.15rem .6rem;margin:.12rem;background:#fff;max-width:100%}
                      .chip img{width:20px;height:20px;border-radius:50%;object-fit:cover;border:1px solid #e9ecef}
                      .chip .name{font-size:12px;color:#212529;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:140px}
                      .chip .code{font-size:11px;color:#6c757d}
                      .chip .qty{font-size:11px;color:#6c757d}
                    </style>
                    <div class="d-flex flex-wrap">
                      @foreach($slot->taiSans as $ts)
                        @php
                          $qty = (int)($ts->pivot->so_luong ?? 0);
                          $code = optional($ts->khoTaiSan)->ma_tai_san ?? ('TS-'.$ts->id);
                          $img = $ts->hinh_anh ? asset('storage/'.$ts->hinh_anh) : (optional($ts->khoTaiSan)->hinh_anh ? asset('storage/'.optional($ts->khoTaiSan)->hinh_anh) : null);
                        @endphp
                        <span class="chip" title="{{ $ts->ten_tai_san }} ({{ $code }}) x{{ $qty }}">
                          @if($img)
                            <img src="{{ $img }}" alt="{{ $ts->ten_tai_san }}">
                          @endif
                          <span class="name">{{ $ts->ten_tai_san }}</span>
                          <span class="code">{{ $code }}</span>
                          <span class="qty">x{{ $qty }}</span>
                        </span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td data-label="Ghi chú"><span class="text-trunc" title="{{ $slot->ghi_chu }}">{{ $slot->ghi_chu }}</span></td>
                <td data-label="Thao tác">
                  <div class="slot-actions" role="group">
                    <button type="button" class="btn-dergin" title="Bàn giao CSVC" onclick="openAssignAssets({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-eye"></i><span>CSVC</span></button>
                    @if (!$slot->sinh_vien_id)
                      <button type="button" class="btn-dergin btn-dergin--info" title="Gán sinh viên" onclick="openAssignStudent({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-user-plus"></i><span>Gán SV</span></button>
                    @else
                      <button type="button" class="btn-dergin btn-dergin--danger" title="Bỏ gán sinh viên" onclick="unassignStudent({{ $slot->id }})"><i class="fa fa-trash"></i><span>Bỏ gán</span></button>
                    @endif
                  </div>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
      </div>
    </div>
  </div>
</div>
{{-- Modal tạo slots đơn giản --}}
<div class="modal fade" id="createSlotsQuickModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="createSlotsQuickForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header"><h5>Tạo slots cho phòng</h5></div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Số lượng slot cần tạo</label>
            <input type="number" min="1" max="50" class="form-control" name="count" value="1" required>
            <small class="text-muted">Mã sẽ tự sinh: {{ $phong->ten_phong }}-S{n} tiếp nối số đang có.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Tạo</button>
        </div>
      </div>
    </form>
  </div>
  </div>
{{-- Modal chọn sinh viên --}}
<div class="modal fade" id="assignStudentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="assignStudentForm">
      @csrf
      <input type="hidden" name="slot_id" id="modal_slot_id">
      <div class="modal-content">
        <div class="modal-header"><h5>Chọn sinh viên để gán vào slot</h5></div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Sinh viên</label>
            <select class="form-select assign-student-select" id="modal_sinh_vien_id" name="sinh_vien_id" required>
              <option value="">--Chọn sinh viên--</option>
              @foreach($sinhViens as $sv)
                <option value="{{ $sv->id }}">{{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }}){{ $sv->gioi_tinh ? ' - '.$sv->gioi_tinh : '' }}</option>
              @endforeach
            </select>
          </div>
          {{-- Bỏ chọn CSVC/ảnh tại đây theo yêu cầu --}}
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Gán</button>
        </div>
      </div>
    </form>
  </div>
</div>
{{-- Modal bàn giao CSVC cho slot (đặt ngoài các modal khác) --}}
<div class="modal fade" id="assignAssetsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <form id="assignAssetsForm" class="modal-content">
      @csrf
      <input type="hidden" id="assign_assets_slot_id" name="slot_id">
      <div class="modal-header">
        <h5 class="modal-title">Bổ sung tài sản cho slot <span id="assign_assets_slot_label" class="text-primary"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
        <div class="modal-body">
        <div class="slot-current-assets mb-4 d-none" data-role="current-assets-wrapper">
          <div class="slot-current-assets__title">CSVC đã bàn giao cho slot</div>
          <div class="slot-current-assets__list" data-role="current-assets-list"></div>
        </div>
        <div class="asset-modal asset-modal--compact">
          <div class="row g-4">
            <div class="col-lg-7">
              <div class="asset-modal__column">
                <h6 class="asset-modal__heading">Danh sách tài sản trong kho</h6>
                <div class="asset-modal__search mb-3">
                  <input type="search" class="form-control form-control-sm" placeholder="Tìm kiếm tài sản..." data-role="asset-search" autocomplete="off" disabled>
                </div>
                <div class="asset-option-list" data-role="asset-picker">
                  <div class="asset-empty-message">Đang tải dữ liệu kho...</div>
                </div>
                <p class="text-muted small mt-3 d-none" data-role="asset-search-empty">Không tìm thấy tài sản phù hợp.</p>
              </div>
            </div>
            <div class="col-lg-5">
              <div class="asset-modal__column">
                <h6 class="asset-modal__heading">Tài sản sẽ bàn giao</h6>
                <div class="selected-assets" data-role="selected-assets">
                  <p class="text-muted small mb-0" data-role="empty-state">Chưa chọn tài sản nào.</p>
          </div>
                <p class="text-muted small mt-3 mb-0">Mỗi tài sản được bàn giao 1 món từ kho tổng.</p>
          </div>
          </div>
          </div>
          </div>
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
        <button type="submit" class="btn btn-primary" data-role="asset-submit" disabled>Bổ sung cho slot</button>
      </div>
    </form>
  </div>
  </div>
{{-- (Đã bỏ modal sửa slot) --}}
@push('scripts')
<script>
  let currentSlotId = null;
  function openCreateSlots(){
    $('#createSlotsQuickModal').modal('show');
  }
  $('#createSlotsQuickForm').submit(function(e){
    e.preventDefault();
    const phongId = {{ $phong->id }};
    const count = parseInt($(this).find('input[name=count]').val()||'0',10);
    if(count<=0){ alert('Số lượng không hợp lệ'); return; }
    const existing = {{ $phong->slots->count() }};
    const codes = [];
    for(let i=1;i<=count;i++){
      const idx = existing + i; // tiếp nối số hiện có
      codes.push('{{ $phong->ten_phong }}-S' + idx);
    }
    const requests = codes.map(code => $.ajax({ url:'/admin/phong/'+phongId+'/slots', method:'POST', data:{ _token:'{{ csrf_token() }}', ma_slot: code }}));
    Promise.allSettled(requests).then(()=> location.reload());
  });
  let __currentAssign = { id: null, ma: '' };
  function openAssignStudent(slotId, maSlot){
    __currentAssign = { id: slotId, ma: maSlot||'' };
    $('#modal_slot_id').val(slotId);
    $('#modal_sinh_vien_id').val('');
    $('#assignStudentModal').modal('show');
  }
  $('#assignStudentForm').submit(function(e){
    e.preventDefault();
    let slotId = $('#modal_slot_id').val();
    let sinhVienId = $('#modal_sinh_vien_id').val();
    if(!sinhVienId) return alert('Chọn sinh viên!');
    const formData = new FormData();
    formData.append('_token','{{ csrf_token() }}');
    formData.append('sinh_vien_id', sinhVienId);
    fetch('/admin/slots/'+slotId+'/assign', { method:'POST', body: formData })
      .then(r=>{ if(!r.ok) throw r; return r.json(); })
      .then(()=> location.reload())
      .catch(async (err)=>{ try{ const j=await err.json(); alert(j.message||'Lỗi'); } catch{ alert('Lỗi'); } });
  });
  // (Đã bỏ nút mở CSVC từ modal gán sinh viên)
  // Bàn giao CSVC từ kho tổng
  const assignAssetsModalEl = document.getElementById('assignAssetsModal');
  const assignAssetsForm = document.getElementById('assignAssetsForm');
  const assetPickerEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-picker"]') : null;
  const selectedAssetsEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="selected-assets"]') : null;
  const searchInputEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-search"]') : null;
  const searchEmptyEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-search-empty"]') : null;
  const submitBtnEl = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="asset-submit"]') : null;
  const currentAssetsWrapper = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="current-assets-wrapper"]') : null;
  const currentAssetsList = assignAssetsModalEl ? assignAssetsModalEl.querySelector('[data-role="current-assets-list"]') : null;
  const assetPlaceholder = '{{ asset('uploads/default.png') }}';

  let assignAssetSelection = new Map();
  let emptyStateEl = null;

  const escapeHtml = (value) => {
    return (value ?? '').toString()
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  };

  const createEmptyStateElement = () => {
    const p = document.createElement('p');
    p.className = 'text-muted small mb-0';
    p.dataset.role = 'empty-state';
    p.textContent = 'Chưa chọn tài sản nào.';
    return p;
  };

  const updateSubmitState = () => {
    if (submitBtnEl) {
      submitBtnEl.disabled = assignAssetSelection.size === 0;
    }
  };

  const updateEmptyState = () => {
    if (!emptyStateEl) {
      return;
    }
    emptyStateEl.classList.toggle('d-none', assignAssetSelection.size > 0);
  };

  const resetSelection = () => {
    assignAssetSelection = new Map();
    if (!selectedAssetsEl) {
      return;
    }
    selectedAssetsEl.innerHTML = '';
    emptyStateEl = createEmptyStateElement();
    selectedAssetsEl.appendChild(emptyStateEl);
    updateEmptyState();
    updateSubmitState();
  };

  const setOptionState = (optionEl, isSelected) => {
    if (!optionEl) {
      return;
    }
    const btn = optionEl.querySelector('[data-role="asset-toggle"]');
    if (isSelected) {
      optionEl.classList.add('is-selected');
      if (btn) {
        btn.textContent = 'Đã chọn';
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-primary');
      }
    } else {
      optionEl.classList.remove('is-selected');
      if (btn) {
        btn.textContent = 'Chọn';
        btn.classList.add('btn-outline-primary');
        btn.classList.remove('btn-primary');
      }
    }
  };

  const removeSelectedItem = (id) => {
    const entry = assignAssetSelection.get(id);
    if (!entry) {
      return;
    }
    if (entry.wrapper && entry.wrapper.parentNode) {
      entry.wrapper.parentNode.removeChild(entry.wrapper);
    }
    assignAssetSelection.delete(id);
    updateEmptyState();
    updateSubmitState();
  };

  const createSelectedItem = (optionEl) => {
    if (!selectedAssetsEl) {
      return;
    }
    const id = optionEl.dataset.id;
    const name = optionEl.dataset.name || 'Không xác định';
    const code = optionEl.dataset.code || 'N/A';
    const stock = optionEl.dataset.stock || '0';
    const condition = optionEl.dataset.condition || 'Không rõ';
    const image = optionEl.dataset.image || assetPlaceholder;

    const wrapper = document.createElement('div');
    wrapper.className = 'selected-asset';
    wrapper.dataset.id = id;

    const top = document.createElement('div');
    top.className = 'selected-asset__top';

    const thumb = document.createElement('div');
    thumb.className = 'selected-asset__thumb';
    const img = document.createElement('img');
    img.src = image;
    img.alt = name;
    thumb.appendChild(img);

    const info = document.createElement('div');
    info.className = 'selected-asset__info';

    const title = document.createElement('div');
    title.className = 'selected-asset__title';
    title.textContent = name;

    const meta = document.createElement('div');
    meta.className = 'selected-asset__meta';
    meta.textContent = `Mã: ${code} · Tồn kho: ${stock}`;

    const conditionEl = document.createElement('div');
    conditionEl.className = 'selected-asset__condition';
    conditionEl.textContent = `Tình trạng: ${condition}`;

    info.appendChild(title);
    info.appendChild(meta);
    info.appendChild(conditionEl);

    const actions = document.createElement('div');
    actions.className = 'selected-asset__actions';

    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn btn-outline-secondary btn-sm';
    removeBtn.textContent = 'Bỏ chọn';
    removeBtn.addEventListener('click', () => {
      removeSelectedItem(id);
      setOptionState(optionEl, false);
    });

    actions.appendChild(removeBtn);

    top.appendChild(thumb);
    top.appendChild(info);
    top.appendChild(actions);

    wrapper.appendChild(top);

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = `assets[${id}]`;
    hiddenInput.value = '1';
    wrapper.appendChild(hiddenInput);

    selectedAssetsEl.appendChild(wrapper);
    assignAssetSelection.set(id, { wrapper, option: optionEl });
    updateEmptyState();
    updateSubmitState();
  };

  const toggleSelection = (optionEl) => {
    const id = optionEl.dataset.id;
    if (!id) {
      return;
    }
    const alreadySelected = assignAssetSelection.has(id);
    if (alreadySelected) {
      removeSelectedItem(id);
      setOptionState(optionEl, false);
      return;
    }
    const stock = parseInt(optionEl.dataset.stock || '0', 10);
    if (stock <= 0) {
      return;
    }
    createSelectedItem(optionEl);
    setOptionState(optionEl, true);
  };

  const bindOptionEvents = (optionEl) => {
    const btn = optionEl.querySelector('[data-role="asset-toggle"]');
    if (btn && !btn.disabled) {
      btn.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();
        toggleSelection(optionEl);
      });
    }
    optionEl.addEventListener('click', (event) => {
      if (event.target.closest('[data-role="asset-toggle"]')) {
        return;
      }
      toggleSelection(optionEl);
    });
  };

  const applySearchFilter = () => {
    if (!assetPickerEl || !searchInputEl) {
      return;
    }
    const keyword = searchInputEl.value.trim().toLowerCase();
    let visible = 0;
    assetPickerEl.querySelectorAll('[data-role="asset-item"]').forEach((optionEl) => {
      const name = (optionEl.dataset.name || '').toLowerCase();
      const code = (optionEl.dataset.code || '').toLowerCase();
      const condition = (optionEl.dataset.condition || '').toLowerCase();
      const matches = !keyword || name.includes(keyword) || code.includes(keyword) || condition.includes(keyword);
      optionEl.classList.toggle('d-none', !matches);
      if (matches) {
        visible += 1;
      }
    });
    if (searchEmptyEl) {
      searchEmptyEl.classList.toggle('d-none', visible !== 0);
    }
  };

  if (searchInputEl) {
    searchInputEl.addEventListener('input', applySearchFilter);
  }

  const buildOptionElement = (asset) => {
    const optionEl = document.createElement('div');
    optionEl.className = 'asset-option';
    optionEl.dataset.role = 'asset-item';
    optionEl.dataset.id = asset.id;
    optionEl.dataset.name = asset.ten_tai_san || '';
    optionEl.dataset.code = asset.ma_tai_san || '';
    optionEl.dataset.stock = asset.so_luong ?? 0;
    optionEl.dataset.condition = asset.tinh_trang || '';
    optionEl.dataset.image = asset.hinh_anh || assetPlaceholder;

    const safeName = escapeHtml(asset.ten_tai_san || 'Không xác định');
    const safeCode = escapeHtml(asset.ma_tai_san || 'N/A');
    const safeCondition = escapeHtml(asset.tinh_trang || 'Không rõ');
    const stock = parseInt(optionEl.dataset.stock || '0', 10);

    optionEl.innerHTML = `
      <div class="asset-option__body">
        <div class="asset-option__thumb">
          <img src="${asset.hinh_anh || assetPlaceholder}" alt="${safeName}">
        </div>
        <div class="asset-option__details">
          <div class="asset-option__title">${safeName}</div>
          <div class="asset-option__meta">Mã: ${safeCode} · Còn: ${Number.isFinite(stock) ? stock : 0}</div>
          <div class="asset-option__condition">Tình trạng: ${safeCondition}</div>
        </div>
        <div class="asset-option__actions">
          <button type="button" class="btn btn-outline-primary btn-sm" data-role="asset-toggle">Chọn</button>
        </div>
      </div>
    `;

    if (stock <= 0) {
      optionEl.classList.add('is-disabled');
      const btn = optionEl.querySelector('[data-role="asset-toggle"]');
      if (btn) {
        btn.textContent = 'Hết hàng';
        btn.disabled = true;
        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-secondary');
      }
    }

    return optionEl;
  };

  const renderAssetList = (assets) => {
    if (!assetPickerEl) {
      return;
    }
    assetPickerEl.innerHTML = '';
    resetSelection();

    if (!Array.isArray(assets) || assets.length === 0) {
      assetPickerEl.innerHTML = '<div class="asset-empty-message">Kho hiện chưa có tài sản khả dụng.</div>';
      if (searchInputEl) {
        searchInputEl.value = '';
        searchInputEl.disabled = true;
      }
      if (searchEmptyEl) {
        searchEmptyEl.classList.add('d-none');
      }
      return;
    }

    const fragment = document.createDocumentFragment();
    assets.forEach((asset) => {
      const optionEl = buildOptionElement(asset);
      fragment.appendChild(optionEl);
    });

    assetPickerEl.appendChild(fragment);
    assetPickerEl.querySelectorAll('[data-role="asset-item"]').forEach(bindOptionEvents);

    if (searchInputEl) {
      searchInputEl.disabled = false;
      searchInputEl.value = '';
    }
    if (searchEmptyEl) {
      searchEmptyEl.classList.add('d-none');
    }
    applySearchFilter();
  };

  const renderCurrentAssets = (items) => {
    if (!currentAssetsWrapper || !currentAssetsList) {
      return;
    }
    currentAssetsList.innerHTML = '';
    if (!Array.isArray(items) || items.length === 0) {
      currentAssetsWrapper.classList.add('d-none');
      return;
    }

    currentAssetsWrapper.classList.remove('d-none');
    const fragment = document.createDocumentFragment();

    items.forEach((item) => {
      const card = document.createElement('div');
      card.className = 'slot-current-asset';

      const thumb = document.createElement('div');
      thumb.className = 'slot-current-asset__thumb';
      const img = document.createElement('img');
      img.src = item.hinh_anh || assetPlaceholder;
      img.alt = item.ten_tai_san || 'Tài sản';
      thumb.appendChild(img);

      const info = document.createElement('div');
      info.className = 'slot-current-asset__info';

      const nameEl = document.createElement('div');
      nameEl.className = 'slot-current-asset__name';
      nameEl.textContent = item.ten_tai_san || 'Không rõ';
      if (!item.is_from_warehouse) {
        const badge = document.createElement('span');
        badge.className = 'slot-current-asset__badge';
        badge.textContent = 'Thêm thủ công';
        nameEl.appendChild(badge);
      }

      const metaEl = document.createElement('div');
      metaEl.className = 'slot-current-asset__meta';
      const metaParts = [];
      if (item.ma_tai_san) {
        metaParts.push('Mã: ' + item.ma_tai_san);
      }
      const quantity = parseInt(item.so_luong || 0, 10);
      metaParts.push('Số lượng: ' + (Number.isFinite(quantity) ? quantity : 0));
      if (item.tinh_trang) {
        metaParts.push('Tình trạng: ' + item.tinh_trang);
      }
      metaEl.textContent = metaParts.join(' · ');

      info.appendChild(nameEl);
      info.appendChild(metaEl);

      card.appendChild(thumb);
      card.appendChild(info);

      fragment.appendChild(card);
    });

    currentAssetsList.appendChild(fragment);
  };

  function openAssignAssets(slotId, maSlot){
    if (!assignAssetsModalEl) {
      return;
    }
    document.getElementById('assign_assets_slot_id').value = slotId;
    document.getElementById('assign_assets_slot_label').textContent = maSlot || '';

    if (assetPickerEl) {
      assetPickerEl.innerHTML = '<div class="asset-empty-message">Đang tải dữ liệu kho...</div>';
    }
    if (searchInputEl) {
      searchInputEl.value = '';
      searchInputEl.disabled = true;
    }
    if (searchEmptyEl) {
      searchEmptyEl.classList.add('d-none');
    }
    if (submitBtnEl) {
      submitBtnEl.disabled = true;
    }
    resetSelection();
    if (currentAssetsWrapper) {
      currentAssetsWrapper.classList.add('d-none');
    }
    if (currentAssetsList) {
      currentAssetsList.innerHTML = '';
    }

    $('#assignAssetsModal').modal('show');

    fetch('/admin/slots/'+slotId+'/warehouse-assets', { headers: { 'Accept': 'application/json' } })
      .then(async (response) => {
        if (!response.ok) {
          const payload = await response.json().catch(() => ({}));
          const message = payload && payload.message ? payload.message : 'Không thể tải dữ liệu kho.';
          throw new Error(message);
        }
        return response.json();
      })
      .then((data) => {
        renderAssetList(data.warehouse_assets || []);
        renderCurrentAssets(data.assigned_assets || []);
      })
      .catch((error) => {
        if (assetPickerEl) {
          assetPickerEl.innerHTML = '<div class="asset-empty-message text-danger">'+escapeHtml(error.message || 'Không thể tải dữ liệu kho.')+'</div>';
        }
      });
  }

  if (assignAssetsForm) {
    assignAssetsForm.addEventListener('submit', function(e){
      e.preventDefault();
      const slotId = document.getElementById('assign_assets_slot_id').value;
      if (!slotId) {
        alert('Không xác định được slot để bổ sung tài sản.');
        return;
      }
      if (assignAssetSelection.size === 0) {
        alert('Vui lòng chọn ít nhất 1 tài sản từ kho.');
        return;
      }

      if (submitBtnEl) {
        submitBtnEl.disabled = true;
      }

      const payload = {
        slot_id: slotId,
        assets: {},
      };

      assignAssetSelection.forEach((entry, id) => {
        payload.assets[id] = 1;
      });

      fetch('{{ route('slots.importFromWarehouse') }}', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify(payload),
      })
        .then(async (response) => {
          if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            throw new Error(data.message || 'Không thể bổ sung tài sản cho slot.');
          }
          return response.json().catch(() => ({}));
        })
        .then(() => {
          location.reload();
        })
        .catch((error) => {
          alert(error.message || 'Không thể bổ sung tài sản cho slot.');
          if (submitBtnEl) {
            submitBtnEl.disabled = false;
          }
        });
    });
  }
  // (Đã bỏ tính năng sửa slot)
  // Bỏ gán sinh viên
  function unassignStudent(slotId){
    if(!confirm('Bạn chắc chắn xoá sinh viên khỏi slot này?')) return;
    $.ajax({
      url: '/admin/slots/'+slotId+'/assign',
      method:'POST',
      data:{ _token:'{{ csrf_token() }}', sinh_vien_id:'' },
      success:()=>location.reload(),
      error:x=>alert(x.responseJSON?.message||'Lỗi')
    });
  }

  // Bỏ toàn bộ CSVC slot
  function clearAssets(slotId){
    if(!confirm('Bỏ gán toàn bộ CSVC cho slot này?')) return;
    fetch('/admin/slots/'+slotId+'/clear-assets', { method:'POST', headers:{ 'X-CSRF-TOKEN':'{{ csrf_token() }}' } })
      .then(r=>{ if(!r.ok) throw new Error('Lỗi'); return r.json(); })
      .then(()=> location.reload())
      .catch(()=> alert('Không thể bỏ CSVC'));
  }
</script>
@endpush
@endsection
