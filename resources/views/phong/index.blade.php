
@extends('admin.layouts.admin')

@section('title','Quản lý phòng')

@section('content')
<div class="container-fluid">
  {{-- Thanh thông báo --}}
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{!! session('error') !!}</div>
  @endif


  {{-- Filter --}}
  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
      <input name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm tên phòng">
    </div>
    <div class="col-md-2">
      <input name="khu" value="{{ request('khu') }}" class="form-control" placeholder="Khu">
    </div>
    <div class="col-md-2">
      <select name="loai_phong" class="form-select">
        <option value="">--Loại phòng--</option>
        <option value="Đơn" {{ request('loai_phong')=='Đơn'?'selected':'' }}>Đơn</option>
        <option value="Đôi" {{ request('loai_phong')=='Đôi'?'selected':'' }}>Đôi</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="gioi_tinh" class="form-select">
        <option value="">--Giới tính--</option>
        <option value="Nam" {{ request('gioi_tinh')=='Nam'?'selected':'' }}>Nam</option>
        <option value="Nữ" {{ request('gioi_tinh')=='Nữ'?'selected':'' }}>Nữ</option>
        <option value="Cả hai" {{ request('gioi_tinh')=='Cả hai'?'selected':'' }}>Cả hai</option>
      </select>
    </div>
    <div class="col-md-2">
      <select name="trang_thai" class="form-select">
        <option value="">--Trạng thái--</option>
        <option value="Trống" {{ request('trang_thai')=='Trống'?'selected':'' }}>Trống</option>
        <option value="Đã ở" {{ request('trang_thai')=='Đã ở'?'selected':'' }}>Đã ở</option>
        <option value="Bảo trì" {{ request('trang_thai')=='Bảo trì'?'selected':'' }}>Bảo trì</option>
      </select>
    </div>
   <div class="col-md-2 text-end d-flex align-items-center justify-content-end">
    <div>
      <button type="submit" class="btn btn-secondary me-2">Lọc</button>
      <a href="{{ route('phong.create') }}" class="btn btn-primary">Thêm phòng</a>
    </div>
  </div>
  </form>

  <p class="mb-2">
    <strong>Tổng:</strong> {{ $totals['total'] }} |
    <strong>Trống:</strong> {{ $totals['trong'] }} |
    <strong>Đã ở:</strong> {{ $totals['da_o'] }} |
    <strong>Bảo trì:</strong> {{ $totals['bao_tri'] }}
  </p>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>Tên phòng</th><th>Khu</th><th>Loại</th><th>Sức chứa</th><th>Giới tính</th><th>Trạng thái</th><th>Hiện tại</th><th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($phongs as $p)
        <tr>
          <td>{{ $p->ten_phong }}</td>
          <td>{{ $p->khu }}</td>
          <td>{{ $p->loai_phong }}</td>
          <td>{{ $p->suc_chua }}</td>
          <td>{{ $p->gioi_tinh ?? '-' }}</td>
          <td>{!! $p->trang_thai == 'Trống' ? '<span class="badge bg-success">Trống</span>' : ($p->trang_thai=='Đã ở' ? '<span class="badge bg-warning">Đã ở</span>' : '<span class="badge bg-danger">Bảo trì</span>') !!}</td>
          <td>{{ $p->soLuongHienTai() }} / {{ $p->suc_chua }}</td>
          <td>
            <a href="{{ route('phong.edit', $p) }}" class="btn btn-sm btn-primary">Sửa</a>
            <form action="{{ route('phong.destroy', $p) }}" method="POST" style="display:inline">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-danger" onclick="return confirm('Xóa phòng?')">Xóa</button>
            </form>
            <button class="btn btn-sm btn-success" onclick="promptAssign({{ $p->id }})">Gán SV</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="8">Không có phòng</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    {{ $phongs->withQueryString()->links() }}
  </div>
</div>

@push('scripts')
<script>
function promptAssign(phongId){
  const svId = prompt('Nhập ID sinh viên muốn gán vào phòng ' + phongId + ':');
  if(!svId) return;
  fetch('/assign/' + svId, {
    method: 'POST',
    headers: {
      'Content-Type':'application/json',
      'X-CSRF-TOKEN':'{{ csrf_token() }}'
    },
    body: JSON.stringify({phong_id: phongId})
  }).then(r => { if(r.redirected) window.location.href = r.url; else return r.text().then(t=>alert(t)); });
}
</script>
@endpush
@endsection
