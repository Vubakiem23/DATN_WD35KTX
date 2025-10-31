@extends('admin.layouts.admin')

@section('title','Chi tiết phòng')

@section('content')
<div class="container">
  <h3>Chi tiết phòng: {{ $phong->ten_phong }}</h3>
  @push('styles')
  <style>
    /* Trang trí riêng cho trang chi tiết phòng */
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
    #assignAssetsModal .modal-dialog{max-width:980px}
    #assignAssetsModal .modal-body{padding:1.5rem}
    #assignAssetsModal .assign-assets-toolbar{gap:.5rem;flex-wrap:wrap}
    #assignAssetsModal .assign-assets-toolbar .input-group{min-width:260px}
    #assignAssetsModal .assign-assets-toolbar .btn{border-radius:8px}
    #assignAssetsModal table thead th{font-size:13px;text-transform:uppercase;letter-spacing:.02em}
    #assignAssetsModal table tbody td{vertical-align:middle}
    #assignAssetsModal .asset-thumb{width:64px;height:64px;border-radius:12px;border:1px solid #e9ecef;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8f9fa;margin:0 auto}
    #assignAssetsModal .asset-thumb img{width:100%;height:100%;object-fit:cover}
    #assignAssetsModal .asset-info .title{font-weight:600;font-size:15px}
    #assignAssetsModal .asset-info .code-badge{font-size:11px;margin-left:.5rem}
    #assignAssetsModal .asset-info .suggest-badge{font-size:11px;margin-left:.5rem}
    #assignAssetsModal .asset-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-top:.4rem}
    #assignAssetsModal .asset-meta span{font-size:12px;color:#6c757d}
    #assignAssetsModal .asset-meta .highlight{color:#0d6efd;font-weight:600}
    #assignAssetsModal .asset-meta .danger{color:#dc3545;font-weight:600}
    #assignAssetsModal .asset-actions .input-group{max-width:180px;margin:0 auto}
    #assignAssetsModal .asset-actions .btn{min-width:36px}
    #assignAssetsModal .empty-placeholder{padding:2rem 0}
    #assignAssetsModal .asset-info .holder-name{font-weight:600;color:#0d6efd}
    #assignAssetsModal .asset-assignees{list-style:none;margin:0;padding:0;margin-top:.6rem}
    #assignAssetsModal .asset-assignees li{display:flex;align-items:center;gap:.5rem;margin-bottom:.25rem;font-size:12px;color:#6c757d}
    #assignAssetsModal .asset-assignees .slot-badge{font-size:11px;background:#e9ecef;color:#495057;border-radius:999px;padding:.15rem .55rem}
    #assignAssetsModal .asset-assignees .qty{text-wrap:nowrap}
    #assignAssetsModal .asset-lock-note{font-size:12px;color:#dc3545;font-weight:600;margin-top:.5rem;display:flex;align-items:center;gap:.35rem}
    #assignAssetsModal .asset-lock-note .fa{font-size:12px}
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
      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Danh sách vị trí (slot)</h5>
          <a href="#" class="btn btn-sm btn-primary" onclick="openCreateSlots()">Tạo slots</a>
        </div>
        <div>
          @push('styles')
          <style>
            /* Fix bảng bị bó cột khiến tiêu đề dọc từng ký tự */
            .slots-table{table-layout:auto;width:100%}
            .slots-table thead th{font-weight:600;white-space:nowrap}
            .slots-table th:nth-child(1){width:88px}
            .slots-table th:nth-child(2){width:220px}
            .slots-table th:nth-child(3){min-width:340px}
            .slots-table th:nth-child(4){width:260px}
            .slots-table th:nth-child(5){width:230px}
            .slots-table td,.slots-table th{vertical-align:middle}
            .slot-actions .btn{margin:.15rem .25rem}
            .slot-actions .btn-action{width:46px;height:38px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px}
            .slot-row.occupied{border-left:3px solid #20c997}
            .slot-row.empty{border-left:3px solid #dee2e6}
            /* Cột CSVC cho phép xuống dòng nội dung nhưng giữ tiêu đề 1 dòng */
            .slots-table td:nth-child(3){white-space:normal}
          </style>
          @endpush
          <table class="table table-striped table-hover align-middle mb-0 slots-table">
            <thead>
              <tr class="table-light">
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
                <td>{{ $slot->ma_slot }}</td>
                <td>{{ $slot->sinhVien->ho_ten ?? '-' }}</td>
                <td>
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
                <td><span class="text-trunc" title="{{ $slot->ghi_chu }}">{{ $slot->ghi_chu }}</span></td>
                <td>
                  <div class="slot-actions" role="group">
                    <button class="btn btn-outline-info btn-action" title="Bàn giao CSVC" onclick="openAssignAssets({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-eye"></i></button>
                    @if (!$slot->sinh_vien_id)
                      <button class="btn btn-outline-success btn-action" title="Gán sinh viên" onclick="openAssignStudent({{ $slot->id }}, '{{ $slot->ma_slot }}')"><i class="fa fa-user-plus"></i></button>
                    @else
                      <button class="btn btn-outline-danger btn-action" title="Bỏ gán sinh viên" onclick="unassignStudent({{ $slot->id }})"><i class="fa fa-trash"></i></button>
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
            <select class="form-control" id="modal_sinh_vien_id" name="sinh_vien_id" required>
              <option value="">--Chọn sinh viên--</option>
              @foreach($sinhViens as $sv)
                <option value="{{ $sv->id }}">{{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }})</option>
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
  <div class="modal-dialog modal-lg">
    <form id="assignAssetsForm">
      @csrf
      <input type="hidden" id="assign_assets_slot_id" name="slot_id">
      <div class="modal-content">
        <div class="modal-header"><h5>Bàn giao CSVC cho slot <span id="assign_assets_slot_label"></span></h5></div>
        <div class="modal-body">
          <div class="d-flex align-items-center assign-assets-toolbar mb-3">
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="fa fa-search"></i></span>
              <input type="text" id="assetsSearch" class="form-control" placeholder="Tìm theo tên tài sản">
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btnFillSuggested">Bộ đề xuất</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnFillAllOne">Gán mỗi loại 1</button>
            <button type="button" class="btn btn-sm btn-outline-danger" id="btnClearAll" disabled>Bỏ hết</button>
            <span class="ms-auto small text-muted" id="assetsSummary">Đang chọn: 0 món</span>
          </div>
          <div id="assetsList" class="table-responsive">
            <table class="table table-sm align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width:90px">Ảnh</th>
                  <th>Thông tin</th>
                  <th class="text-center" style="width:240px">Tình trạng & số lượng</th>
                  <th class="text-center" style="width:210px">Gán cho slot</th>
                </tr>
              </thead>
              <tbody id="assetsRows"></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-success">Lưu bàn giao</button>
        </div>
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
  // Bàn giao CSVC
  function openAssignAssets(slotId, maSlot){
    document.getElementById('assign_assets_slot_id').value = slotId;
    document.getElementById('assign_assets_slot_label').textContent = maSlot;
    const tbody = document.getElementById('assetsRows');
    const listWrapper = document.getElementById('assetsList');
    const searchInput = document.getElementById('assetsSearch');
    const summaryEl = document.getElementById('assetsSummary');
    const btnSuggested = document.getElementById('btnFillSuggested');
    const btnFillOne = document.getElementById('btnFillAllOne');
    const btnClear = document.getElementById('btnClearAll');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Đang tải...</td></tr>';
    summaryEl.textContent = 'Đang chọn: 0 món';
    btnClear.disabled = true;
    searchInput.value = '';
    $('#assignAssetsModal').modal('show');

    const escapeHtml = (value) => {
      return (value ?? '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    };

    fetch('/admin/slots/'+slotId+'/assets', { headers: { 'Accept': 'application/json' }})
      .then(r=>r.json())
      .then(data => {
        window.__assetsCache = (data.assets||[]).map(item => {
          const rawOthers = Array.isArray(item.assigned_to_other_slots) ? item.assigned_to_other_slots : (Array.isArray(item.dang_duoc_giu) ? item.dang_duoc_giu : []);
          const normalizedOthers = rawOthers.map(other => ({
            slot_id: other.slot_id,
            ma_slot: other.ma_slot,
            so_luong: parseInt(other.so_luong||0,10)||0,
            sinh_vien: other.sinh_vien ? {
              id: other.sinh_vien.id,
              ho_ten: other.sinh_vien.ho_ten,
              ma_sinh_vien: other.sinh_vien.ma_sinh_vien
            } : null
          }));
          return {
            ...item,
            id: item.id,
            ten_tai_san: item.ten_tai_san,
            ma: item.ma,
            tinh_trang: item.tinh_trang,
            so_luong_phong: parseInt(item.so_luong_phong||0,10)||0,
            da_gan_cho_slot_nay: parseInt(item.da_gan_cho_slot_nay||0,10)||0,
            con_lai_co_the_gan: parseInt(item.con_lai_co_the_gan||0,10)||0,
            extra_capacity: parseInt(item.extra_capacity||0,10)||0,
            khong_the_gan_them: Boolean(item.khong_the_gan_them),
            assigned_to_other_slots: normalizedOthers
          };
        });

        btnFillOne.disabled = (window.__assetsCache||[]).every(a => (parseInt(a.con_lai_co_the_gan,10)||0) <= 0);
        btnSuggested.disabled = !(window.__assetsCache||[]).some(a => a.suggested);

        const updateSummary = () => {
          const total = (window.__assetsCache||[]).reduce((sum, a) => sum + (parseInt(a.da_gan_cho_slot_nay||0,10)||0), 0);
          summaryEl.innerHTML = 'Đang chọn: <strong>'+ total +'</strong> món';
          btnClear.disabled = total === 0;
        };

        const render = () => {
          const term = (searchInput.value||'').toLowerCase().trim();
          const assets = (window.__assetsCache||[]).filter(a => !term || (a.ten_tai_san||'').toLowerCase().includes(term));
          const rows = assets.map(a => {
            const current = parseInt(a.da_gan_cho_slot_nay||0,10)||0;
            const max = parseInt(a.con_lai_co_the_gan||0,10)||0;
            const imgHtml = a.hinh_anh
              ? '<img src="'+escapeHtml(a.hinh_anh)+'" alt="'+escapeHtml(a.ten_tai_san||'')+'">'
              : '<i class="fa fa-cube text-muted"></i>';
            const remainClass = max === 0 ? 'danger' : 'highlight';
            const disableDec = current <= 0 ? ' disabled' : '';
            const disableInc = (max <= 0 || current >= max) ? ' disabled' : '';
            const assignedOthers = Array.isArray(a.assigned_to_other_slots) ? a.assigned_to_other_slots : [];
            const assignmentsList = assignedOthers.length ? '<ul class="asset-assignees">'+assignedOthers.map(info => {
              const slotLabel = 'Slot '+escapeHtml(info.ma_slot || ('#'+info.slot_id));
              let holderText = 'Slot chưa có sinh viên';
              if (info.sinh_vien) {
                holderText = info.sinh_vien.ho_ten || '';
                if (info.sinh_vien.ma_sinh_vien) {
                  holderText += ' ('+info.sinh_vien.ma_sinh_vien+')';
                }
              }
              const holder = escapeHtml(holderText);
              return '<li><span class="slot-badge">'+slotLabel+'</span><span class="holder-name">'+holder+'</span><span class="qty">• '+info.so_luong+' món</span></li>';
            }).join('')+'</ul>' : '';
            const lockNotice = a.khong_the_gan_them && assignedOthers.length
              ? '<div class="asset-lock-note"><i class="fa fa-lock"></i><span>Đã cấp hết cho slot khác. Thu hồi trước khi gán thêm.</span></div>'
              : '';
            return '<tr>'+
              '<td class="text-center"><div class="asset-thumb">'+imgHtml+'</div></td>'+
              '<td>'+
                '<div class="asset-info">'+
                  '<div class="d-flex align-items-center flex-wrap gap-2">'+
                    '<span class="title">'+escapeHtml(a.ten_tai_san||'')+'</span>'+
                    '<span class="badge bg-light text-dark border code-badge">'+escapeHtml(a.ma || '-')+'</span>'+
                    (a.suggested ? '<span class="badge bg-info text-dark suggest-badge">Đề xuất</span>' : '')+
                  '</div>'+
                  (a.tinh_trang ? '<div class="small text-muted mt-1">Tình trạng: '+escapeHtml(a.tinh_trang)+'</div>' : '')+
                  assignmentsList+
                '</div>'+
              '</td>'+
              '<td class="text-center">'+
                '<div class="asset-meta justify-content-center">'+
                  '<span>Tổng (phòng): <span class="highlight">'+a.so_luong_phong+'</span></span>'+
                  '<span>Đã gán (slot): <span class="highlight">'+current+'</span></span>'+
                  '<span class="'+remainClass+'">Còn có thể gán: '+max+'</span>'+
                '</div>'+lockNotice+
              '</td>'+
              '<td class="asset-actions">'+
                '<div class="input-group input-group-sm">'+
                  '<button class="btn btn-outline-secondary btn-dec" data-id="'+a.id+'" type="button"'+disableDec+'>-</button>'+
                  '<input type="number" class="form-control text-center asset-assign" data-id="'+a.id+'" min="0" max="'+max+'" value="'+current+'">'+
                  '<button class="btn btn-outline-secondary btn-inc" data-id="'+a.id+'" type="button"'+disableInc+'>+</button>'+
                '</div>'+
              '</td>'+
            '</tr>';
          }).join('');
          tbody.innerHTML = rows || '<tr><td colspan="4" class="text-center text-muted empty-placeholder">Phòng chưa có tài sản</td></tr>';
          updateSummary();
        };

        window.__renderAssetsTable = render;
        window.__updateAssetsSummary = updateSummary;

        searchInput.oninput = () => render();
        btnFillOne.onclick = () => {
          (window.__assetsCache||[]).forEach(a => {
            if ((parseInt(a.con_lai_co_the_gan||0,10)||0) > 0) {
              a.da_gan_cho_slot_nay = Math.min(1, parseInt(a.con_lai_co_the_gan||0,10)||0);
            }
          });
          render();
        };
        btnClear.onclick = () => {
          (window.__assetsCache||[]).forEach(a => { a.da_gan_cho_slot_nay = 0; });
          render();
        };
        btnSuggested.onclick = () => {
          (window.__assetsCache||[]).forEach(a => {
            if (a.suggested && (parseInt(a.con_lai_co_the_gan||0,10)||0) > 0 && !a.da_gan_cho_slot_nay) {
              a.da_gan_cho_slot_nay = 1;
            }
          });
          render();
        };

        if (!listWrapper.dataset.bound) {
          listWrapper.addEventListener('click', function(ev){
            const btn = ev.target.closest('.btn-inc,.btn-dec'); if(!btn) return;
            const id = btn.getAttribute('data-id');
            const idx = (window.__assetsCache||[]).findIndex(a => String(a.id) === String(id));
            if (idx < 0) return;
            let current = parseInt(window.__assetsCache[idx].da_gan_cho_slot_nay||0,10)||0;
            const max = parseInt(window.__assetsCache[idx].con_lai_co_the_gan||0,10)||0;
            if (btn.classList.contains('btn-inc')) {
              current = Math.min(max, current + 1);
            } else {
              current = Math.max(0, current - 1);
            }
            window.__assetsCache[idx].da_gan_cho_slot_nay = current;
            if (typeof window.__renderAssetsTable === 'function') { window.__renderAssetsTable(); }
          });
          listWrapper.addEventListener('input', function(ev){
            const inp = ev.target.closest('.asset-assign'); if(!inp) return;
            const id = inp.getAttribute('data-id');
            const idx = (window.__assetsCache||[]).findIndex(a => String(a.id) === String(id));
            if (idx < 0) return;
            let val = parseInt(inp.value||'0',10)||0;
            const max = parseInt(window.__assetsCache[idx].con_lai_co_the_gan||0,10)||0;
            if (val < 0) val = 0;
            if (val > max) val = max;
            window.__assetsCache[idx].da_gan_cho_slot_nay = val;
            inp.value = val;
            if (typeof window.__updateAssetsSummary === 'function') { window.__updateAssetsSummary(); }
          });
          listWrapper.dataset.bound = 'true';
        }

        render();
      })
      .catch(()=>{ tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Lỗi tải dữ liệu</td></tr>'; });
  }

  document.getElementById('assignAssetsForm').addEventListener('submit', function(e){
    e.preventDefault();
    const slotId = document.getElementById('assign_assets_slot_id').value;
    const payload = { _token: '{{ csrf_token() }}', assets: {} };
    (window.__assetsCache||[]).forEach(a => { payload.assets[a.id] = parseInt(a.da_gan_cho_slot_nay||0,10)||0; });
    fetch('/admin/slots/'+slotId+'/assign-assets', {
      method:'POST', headers:{ 'Accept':'application/json','Content-Type':'application/json' },
      body: JSON.stringify(payload)
    })
      .then(async r=>{ if(!r.ok){ const j=await r.json(); throw new Error(j.message||'Lỗi'); } return r.json(); })
      .then(()=>{ location.reload(); })
      .catch(err=>{ alert(err.message||'Lỗi'); });
  });
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
