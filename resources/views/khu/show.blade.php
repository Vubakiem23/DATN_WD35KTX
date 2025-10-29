@extends('admin.layouts.admin')
@section('title','Chi tiết khu')

@section('content')
<div class="container">
  @push('styles')
  <style>
    .khu-header-actions .btn-action, .khu-show-actions .btn-action{width:40px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px}
    .khu-show-actions .btn-action i{font-size:14px}
    .stat-card .card-body{min-height:110px;display:flex;flex-direction:column;align-items:center;justify-content:center}
    .stat-card .display-6{font-weight:600}
    .table.khu-table td,.table.khu-table th{vertical-align:middle}
    .khu-show-actions .btn-action{margin:0 .2rem}
  </style>
  @endpush

  <div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h5 class="mb-0">Chi tiết khu {{ $khu->ten_khu }}</h5>
        <small class="text-muted">Giới tính: {{ $khu->gioi_tinh }}</small>
      </div>
      <div class="khu-header-actions">
        <a href="{{ route('khu.index') }}" class="btn btn-outline-secondary btn-action" title="Quay lại"><i class="fa fa-arrow-left"></i></a>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-3 justify-content-center">
    <div class="col-sm-6 col-lg-3">
      <div class="card text-center stat-card shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">Tổng phòng</div>
          <div class="display-6">{{ $stats['total'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-center stat-card shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">Phòng trống slots</div>
          <div class="display-6 text-success">{{ $stats['hasAvailable'] }}</div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-lg-3">
      <div class="card text-center stat-card shadow-sm">
        <div class="card-body">
          <div class="h5 mb-1">Phòng đã full</div>
          <div class="display-6 text-danger">{{ $stats['full'] }}</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-body p-0">
      <div class="p-3 border-bottom bg-light">
        <div class="row g-2 align-items-center">
          <div class="col-sm-6 col-lg-4">
            <input type="text" id="filterName" class="form-control" placeholder="Tìm theo tên phòng...">
          </div>
          <div class="col-sm-6 col-lg-3">
            <select id="filterStatus" class="form-control">
              <option value="all">Tất cả trạng thái</option>
              <option value="empty">Trống</option>
              <option value="partial">Còn chỗ</option>
              <option value="full">Đã ở full</option>
              <option value="no-slot">Chưa có slot</option>
            </select>
          </div>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0 khu-table">
          <thead>
            <tr>
              <th>Tên phòng</th>
              <th>Loại</th>
              <th>Slots</th>
              <th>Đang ở</th>
              <th>Trống</th>
              <th>Trạng thái</th>
              <th class="text-end" style="width:120px">Hành động</th>
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
              <td>{{ $p->ten_phong }}</td>
              <td>{{ \App\Models\Phong::labelLoaiPhongBySlots((int)$p->total_slots) }}</td>
              <td>{{ (int)$p->total_slots }}</td>
              <td>{{ (int)$p->used_slots }}</td>
              <td>{{ $available }}</td>
              <td>
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
              <td class="text-end khu-show-actions">
                <a href="{{ route('phong.edit', $p->id) }}" class="btn btn-outline-primary btn-action" title="Sửa"><i class="fa fa-pencil"></i></a>
                <a href="{{ route('phong.show', $p->id) }}" class="btn btn-outline-secondary btn-action" title="Chi tiết"><i class="fa fa-eye"></i></a>
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



