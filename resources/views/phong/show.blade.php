@extends('admin.layouts.admin')

@section('title','Chi tiết phòng')

@section('content')
<div class="container">
  <h3>Chi tiết phòng: {{ $phong->ten_phong }}</h3>
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
    @media (max-width: 992px){
      .slots-table th:nth-child(1){width:20%}
      .slots-table th:nth-child(2){width:25%}
      .slots-table th:nth-child(3){width:25%}
      .slots-table th:nth-child(4){width:20%}
      .slots-table th:nth-child(5){width:10%}
      .slots-table th:nth-child(6){width:20%}
    }
  </style>
  <div class="row g-3">
    <div class="col-12 col-lg-4">
      <div class="card mb-3 shadow-sm h-100">
    @if($phong->hinh_anh)
      <img src="{{ asset('storage/'.$phong->hinh_anh) }}" class="room-cover" alt="{{ $phong->ten_phong }}">
    @endif
    <div class="card-body">
      <div class="d-flex flex-wrap gap-2 mb-2">
        <span class="badge bg-primary">Khu: {{ $phong->khu ?: '-' }}</span>
        <span class="badge bg-info text-dark">Loại: {{ $phong->loai_phong ?: \App\Models\Phong::labelLoaiPhongBySlots($phong->totalSlots()) }}</span>
        <span class="badge bg-secondary">Sức chứa: {{ $phong->totalSlots() }}</span>
        <span class="badge {{ $phong->availableSlots()==0 ? 'bg-warning text-dark' : 'bg-success' }}">{{ $phong->usedSlots() }} / {{ $phong->totalSlots() }} ({{ $phong->occupancyLabel() }})</span>
      </div>
      <div class="progress mb-3" style="height:10px;max-width:420px;">
        @php $total=$phong->totalSlots(); $used=$phong->usedSlots(); $pct=$total?round($used*100/$total):0; @endphp
        <div class="progress-bar {{ $pct==100 ? 'bg-warning text-dark' : 'bg-success' }}" role="progressbar" style="width: {{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
      @if($phong->ghi_chu)
        <p class="text-muted mb-0"><i class="fa fa-sticky-note-o me-1"></i>{{ $phong->ghi_chu }}</p>
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
          <table class="table table-striped table-hover align-middle mb-0 slots-table">
            <thead>
              <tr class="table-light">
                <th>Mã slot</th>
                <th>Sinh viên</th>
                <th>Cơ sở vật chất</th>
                <th>Ghi chú</th>
                <th>Ảnh thực tế</th>
                <th>Thao tác</th>
              </tr>
            </thead>
            <tbody>
              @foreach($phong->slots as $slot)
              <tr>
                <td>{{ $slot->ma_slot }}</td>
                <td>{{ $slot->sinhVien->ho_ten ?? '-' }}</td>
                <td><span class="text-trunc" title="{{ $slot->cs_vat_chat }}">{{ $slot->cs_vat_chat }}</span></td>
                <td><span class="text-trunc" title="{{ $slot->ghi_chu }}">{{ $slot->ghi_chu }}</span></td>
                <td>
                  @if ($slot->hinh_anh)
                    <img src="{{ asset('storage/'.$slot->hinh_anh) }}" alt="Ảnh slot" class="slot-thumb">
                  @else -
                  @endif
                </td>
                <td>
                  <div class="slot-actions" role="group">
                    <button class="btn btn-sm btn-secondary btn-edit-slot"
                      data-slot-id="{{ $slot->id }}"
                      data-ma-slot="{{ $slot->ma_slot }}"
                      data-cs-vat-chat="{{ $slot->cs_vat_chat }}"
                      data-ghi-chu="{{ $slot->ghi_chu }}"
                    >Sửa</button>
                    @if (!$slot->sinh_vien_id)
                      <button class="btn btn-sm btn-success" onclick="openAssignStudent({{ $slot->id }})">Gán sinh viên</button>
                    @else
                      <button class="btn btn-sm btn-danger" onclick="unassignStudent({{ $slot->id }})">Bỏ gán</button>
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
          <div class="form-group mb-2">
            <label>Cơ sở vật chất riêng cho slot</label>
            <textarea class="form-control" id="modal_cs_vat_chat" name="cs_vat_chat" rows="2" placeholder="VD: Giường, Đệm, Quạt cá nhân..."></textarea>
          </div>
          <div class="form-group mb-2">
            <label>Ảnh thực tế slot (tùy chọn)</label>
            <input type="file" class="form-control" id="modal_hinh_anh" name="hinh_anh" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Gán</button>
        </div>
      </div>
    </form>
  </div>
</div>
{{-- Modal sửa slot --}}
<div class="modal fade" id="editSlotModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editSlotForm">
      @csrf
      <input type="hidden" id="edit_slot_id" name="slot_id">
      <div class="modal-content">
        <div class="modal-header"><h5>Sửa slot</h5></div>
        <div class="modal-body">
          <div class="form-group mb-2">
            <label>Mã slot</label>
            <input type="text" class="form-control" id="edit_ma_slot" name="ma_slot" disabled>
            <small class="text-muted">Mã slot cố định để tránh trùng và sai tham chiếu.</small>
          </div>
          <div class="form-group mb-2">
            <label>Cơ sở vật chất</label>
            <textarea class="form-control" id="edit_cs_vat_chat" name="cs_vat_chat" rows="2"></textarea>
          </div>
          <div class="form-group mb-2">
            <label>Ghi chú</label>
            <textarea class="form-control" id="edit_ghi_chu" name="ghi_chu" rows="2"></textarea>
          </div>
          <div class="form-group mb-2">
            <label>Ảnh thực tế (tùy chọn)</label>
            <input type="file" class="form-control" id="edit_hinh_anh" name="hinh_anh" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
          <button type="submit" class="btn btn-success">Lưu</button>
        </div>
      </div>
    </form>
  </div>
  </div>
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
  function openAssignStudent(slotId){
    $('#modal_slot_id').val(slotId);
    $('#modal_sinh_vien_id').val('');
    $('#assignStudentModal').modal('show');
  }
  $('#assignStudentForm').submit(function(e){
    e.preventDefault();
    let slotId = $('#modal_slot_id').val();
    let sinhVienId = $('#modal_sinh_vien_id').val();
    let csVatChat = $('#modal_cs_vat_chat').val();
    if(!sinhVienId) return alert('Chọn sinh viên!');
    const formData = new FormData();
    formData.append('_token','{{ csrf_token() }}');
    formData.append('sinh_vien_id', sinhVienId);
    formData.append('cs_vat_chat', csVatChat||'');
    const file = document.getElementById('modal_hinh_anh').files[0];
    if(file) formData.append('hinh_anh', file);
    fetch('/admin/slots/'+slotId+'/assign', { method:'POST', body: formData })
      .then(r=>{ if(!r.ok) throw r; return r.json(); })
      .then(()=> location.reload())
      .catch(async (err)=>{ try{ const j=await err.json(); alert(j.message||'Lỗi'); } catch{ alert('Lỗi'); } });
  });
  // Edit slot
  // open edit with data attributes (an toàn ký tự)
  $(document).on('click', '.btn-edit-slot', function(){
    const id = this.dataset.slotId;
    const ma = this.dataset.maSlot || '';
    const csvc = this.dataset.csVatChat || '';
    const ghichu = this.dataset.ghiChu || '';
    $('#edit_slot_id').val(id);
    $('#edit_ma_slot').val(ma);
    $('#edit_cs_vat_chat').val(csvc);
    $('#edit_ghi_chu').val(ghichu);
    $('#edit_hinh_anh').val('');
    $('#editSlotModal').modal('show');
  });
  $('#editSlotForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#edit_slot_id').val();
    const formData = new FormData(this);
    $.ajax({
      url: '/admin/slots/'+id+'/update',
      method: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: { 'Accept': 'application/json' },
      success: function(){ location.reload(); },
      error: function(xhr){
        let msg = 'Lỗi';
        if (xhr.responseJSON) {
          if (xhr.responseJSON.message) msg = xhr.responseJSON.message;
          if (xhr.responseJSON.errors) {
            const firstKey = Object.keys(xhr.responseJSON.errors)[0];
            if (firstKey) msg = xhr.responseJSON.errors[firstKey][0];
          }
        } else if (xhr.responseText) {
          msg = xhr.status+': '+xhr.responseText.substring(0,200);
        }
        alert(msg);
      }
    });
  });
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
</script>
@endpush
@endsection
