@extends('admin.layouts.admin')
@section('title','Chi tiết khu')

@section('content')
<div class="container-fluid khu-detail-page">
  @push('styles')
  <style>
    .khu-detail-page{padding-bottom:2rem}
    .khu-page__title{font-size:1.75rem;font-weight:700;color:#1f2937}
    .btn-dergin{display:inline-flex;align-items:center;gap:.4rem;padding:.45rem 1rem;border-radius:999px;font-weight:600;font-size:.78rem;border:none;color:#fff;background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%);box-shadow:0 8px 20px rgba(79,70,229,.25);text-decoration:none}
    .btn-dergin:hover{transform:translateY(-1px);box-shadow:0 12px 24px rgba(79,70,229,.35);color:#fff}
    .btn-dergin i{font-size:.85rem}
    .btn-dergin--muted{background:linear-gradient(135deg,#4f46e5 0%,#6366f1 100%)}
    .btn-dergin--info{background:linear-gradient(135deg,#22c55e 0%,#16a34a 100%)}
    .khu-stat-card{background:linear-gradient(135deg,#f3f4f6 0%,#ffffff 100%);border-radius:16px;padding:1.25rem 1.5rem;box-shadow:0 12px 30px rgba(15,23,42,0.08);display:flex;flex-direction:column;gap:.35rem;height:100%}
    .khu-stat-card__label{text-transform:uppercase;font-size:.75rem;letter-spacing:.08em;color:#6b7280}
    .khu-stat-card__value{font-size:1.75rem;font-weight:700;color:#111827}
    .khu-toolbar{background:#ffffff;border-radius:14px;padding:1rem 1.25rem;box-shadow:0 10px 24px rgba(15,23,42,0.06)}
    .khu-rooms-wrapper{background:#fff;border-radius:16px;box-shadow:0 12px 28px rgba(15,23,42,0.08);padding:1.25rem}
    .khu-rooms-table{margin-bottom:0;border-collapse:separate;border-spacing:0 12px}
    .khu-rooms-table thead th{border:none;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;padding-bottom:.75rem}
    .khu-rooms-table tbody tr{background:#f8fafc;border-radius:16px;transition:transform .2s ease,box-shadow .2s ease}
    .khu-rooms-table tbody tr:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(15,23,42,0.12)}
    .khu-rooms-table tbody td{border:none;vertical-align:middle;padding:1rem 1rem}
    .khu-rooms-table tbody tr td:first-child{border-top-left-radius:16px;border-bottom-left-radius:16px}
    .khu-rooms-table tbody tr td:last-child{border-top-right-radius:16px;border-bottom-right-radius:16px}
    .khu-show-actions{display:flex;justify-content:flex-end;gap:.5rem}
    @media (max-width:992px){
      .khu-rooms-table thead{display:none}
      .khu-rooms-table tbody{display:block}
      .khu-rooms-table tbody tr{display:flex;flex-direction:column;padding:1.1rem}
      .khu-rooms-table tbody td{display:flex;justify-content:space-between;padding:.45rem 0}
      .khu-rooms-table tbody td[data-label]{position:relative;padding-left:130px}
      .khu-rooms-table tbody td[data-label]::before{content:attr(data-label);position:absolute;left:0;font-weight:600;color:#6b7280;text-transform:uppercase;font-size:.7rem;letter-spacing:.05em}
      .khu-show-actions{justify-content:flex-start}
    }
  </style>
  @endpush


    <div>
      <h3 class="khu-page__title mb-1">Chi tiết khu {{ $khu->ten_khu }}</h3>
      <small class="text-muted">Giới tính: {{ $khu->gioi_tinh }}</small>
    </div>
    <div class="khu-header-actions">
      <a href="{{ route('khu.index') }}" class="btn btn-dergin btn-dergin--muted" title="Quay lại"><i class="fa fa-arrow-left"></i><span>Về danh sách</span></a>
    </div>
 

  <div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
      <div class="khu-stat-card">
        <span class="khu-stat-card__label">Tổng phòng</span>
        <span class="khu-stat-card__value">{{ $stats['total'] }}</span>
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
      <div class="khu-stat-card">
        <span class="khu-stat-card__label">Phòng trống slots</span>
        <span class="khu-stat-card__value text-success">{{ $stats['hasAvailable'] }}</span>
      </div>
    </div>
    <div class="col-sm-6 col-lg-4">
      <div class="khu-stat-card">
        <span class="khu-stat-card__label">Phòng đã full</span>
        <span class="khu-stat-card__value text-danger">{{ $stats['full'] }}</span>
      </div>
    </div>
  </div>

  <div class="khu-toolbar mb-3">
    <div class="row g-2 align-items-center">
      <div class="col-sm-6 col-lg-4">
        <input type="text" id="filterName" class="form-control" placeholder="Tìm theo tên phòng...">
      </div>
      <div class="col-sm-6 col-lg-3">
        <select id="filterStatus" class="form-select">
          <option value="all">Tất cả trạng thái</option>
          <option value="empty">Trống</option>
          <option value="partial">Còn chỗ</option>
          <option value="full">Đã ở full</option>
          <option value="no-slot">Chưa có slot</option>
        </select>
      </div>
    </div>
  </div>
  <div class="khu-rooms-wrapper">
      <div class="table-responsive">
        <table class="table align-middle khu-rooms-table">
          <thead>
            <tr>
              <th>Tên phòng</th>
              <th>Loại</th>
              <th>Slots</th>
              <th>Đang ở</th>
              <th>Trống</th>
              <th>Trạng thái</th>
              <th class="text-end">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse($phongs as $p)
            @php $available = max(0, (int)$p->total_slots - (int)$p->used_slots); @endphp
            @php
              $status = 'partial';
              if ((int)$p->total_slots === 0) { $status = 'no-slot'; }
              elseif ($available === 0) { $status = 'full'; }
              elseif ($p->used_slots === 0) { $status = 'empty'; }
            @endphp
            <tr data-name="{{ Str::lower($p->ten_phong) }}" data-status="{{ $status }}">
              <td data-label="Tên phòng">{{ $p->ten_phong }}</td>
              <td data-label="Loại">{{ \App\Models\Phong::labelLoaiPhongBySlots((int)$p->total_slots) }}</td>
              <td data-label="Slots">{{ (int)$p->total_slots }}</td>
              <td data-label="Đang ở">{{ (int)$p->used_slots }}</td>
              <td data-label="Trống">{{ $available }}</td>
              <td data-label="Trạng thái">
                @if($status === 'no-slot')
                  <span class="badge bg-secondary">Chưa có slot</span>
                @elseif($status === 'full')
                  <span class="badge bg-warning text-dark">Đã ở full</span>
                @elseif($status === 'empty')
                  <span class="badge bg-success">Trống {{ $available }}</span>
                @else
                  <span class="badge bg-info text-dark">Còn chỗ {{ $available }}</span>
                @endif
              </td>
              <td class="text-end" data-label="Hành động">
                <div class="khu-show-actions">
                  <a href="{{ route('phong.edit', $p->id) }}" class="btn btn-dergin" title="Sửa"><i class="fa fa-pencil"></i><span>Sửa</span></a>
                  <a href="{{ route('phong.show', $p->id) }}" class="btn btn-dergin btn-dergin--muted" title="Chi tiết"><i class="fa fa-eye"></i><span>Chi tiết</span></a>
                </div>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center p-4 text-muted">Khu này chưa có phòng</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (function(){
    function applyFilter(){
      var term = (document.getElementById('filterName').value || '').toLowerCase();
      var st = (document.getElementById('filterStatus').value || 'all');
      document.querySelectorAll('.khu-table tbody tr').forEach(function(row){
        var name = (row.getAttribute('data-name')||'').toLowerCase();
        var status = row.getAttribute('data-status')||'';
        var okName = !term || name.indexOf(term) !== -1;
        var okStatus = (st==='all') || (status===st);
        row.style.display = (okName && okStatus) ? '' : 'none';
      });
    }
    document.getElementById('filterName').addEventListener('input', applyFilter);
    document.getElementById('filterStatus').addEventListener('change', applyFilter);
  })();
</script>
@endpush



