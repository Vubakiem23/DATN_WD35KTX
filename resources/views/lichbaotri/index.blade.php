@extends('admin.layouts.admin')

@section('title', 'Danh sách lịch bảo trì')

@section('content')
<div class="container mt-4">
  <h4 class="mb-3">🛠️ Danh sách lịch bảo trì</h4>

  {{-- Thông báo --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <a href="{{ route('lichbaotri.create') }}" class="btn btn-success mb-3">➕ Lên lịch mới</a>

  <table class="table table-bordered table-striped align-middle table-hover">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Hình ảnh</th>
        <th>Tài sản</th>
        <th>Vị trí</th>
        <th>Ngày bảo trì</th>
        <th>Ngày hoàn thành</th>
        <th>Trạng thái</th>
        <th>Mô tả</th>
        <th class="text-center" style="width: 180px;">Hành động</th>
      </tr>
    </thead>
    <tbody>
      @forelse($lich as $index => $l)
      <tr class="{{ $l->trang_thai == 'Hoàn thành' ? 'table-success' : '' }}">
        <td>{{ $lich->firstItem() + $index }}</td>

        {{-- Hình ảnh --}}
        <td class="text-center">
          @if($l->hinh_anh && file_exists(public_path('uploads/lichbaotri/'.$l->hinh_anh)))
            <img src="{{ asset('uploads/lichbaotri/'.$l->hinh_anh) }}" 
                 alt="Ảnh bảo trì"
                 style="width:70px;height:70px;object-fit:cover;border-radius:8px;border:1px solid #ddd;">
          @else
            <div class="bg-light text-muted d-flex align-items-center justify-content-center border rounded"
                 style="width:70px;height:70px;">
              <small>Không có ảnh</small>
            </div>
          @endif
        </td>

        <td>{{ $l->taiSan->ten_tai_san ?? $l->khoTaiSan->ten_tai_san ?? 'Không xác định' }}</td>
        <td>{{ $l->taiSan->phong->ten_phong ?? ($l->khoTaiSan ? 'Kho' : '-') }}</td>
        <td>{{ $l->ngay_bao_tri }}</td>
        <td>{{ $l->ngay_hoan_thanh ?? '-' }}</td>
        <td>
          <span class="badge 
            @if($l->trang_thai == 'Hoàn thành') bg-success
            @elseif($l->trang_thai == 'Đang bảo trì') bg-warning text-dark
            @else bg-secondary @endif">
            {{ $l->trang_thai }}
          </span>
        </td>
        <td>{{ $l->mo_ta ?? '-' }}</td>

        <td class="text-center">
          <button type="button" class="btn btn-secondary btn-sm mb-1 openModalBtn" data-id="{{ $l->id }}">Chi tiết</button>
          <a href="{{ route('lichbaotri.edit', $l->id) }}" class="btn btn-warning btn-sm mb-1">✏️ Sửa</a>
          <form action="{{ route('lichbaotri.destroy', $l->id) }}" method="POST" class="d-inline mb-1"
                onsubmit="return confirm('Bạn có chắc muốn xóa lịch này không?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">🗑️ Xóa</button>
          </form>
          @if($l->trang_thai != 'Hoàn thành')
          <form action="{{ route('lichbaotri.hoanthanh', $l->id) }}" method="POST" class="d-inline mb-1">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Đánh dấu hoàn thành?')">✅ Hoàn thành</button>
          </form>
          @endif
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="9" class="text-center text-muted">Không có lịch bảo trì nào</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Phân trang --}}
  <div class="d-flex justify-content-center mt-3">
    {{ $lich->appends(request()->query())->links('pagination::bootstrap-5') }}
  </div>
</div>

{{-- Modal chi tiết --}}
<div class="modal fade" id="lichModal" tabindex="-1" aria-labelledby="lichModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="lichModalLabel">Chi tiết lịch bảo trì</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" id="modalBody">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('.openModalBtn').on('click', function() {
    let id = $(this).data('id');
    getChiTietLich(id);
    $('#lichModal').modal('show');
  });
});

function getChiTietLich(id) {
  let url = `{{ route('lichbaotri.show.modal', ['id'=>':id']) }}`;
  url = url.replace(':id', id);

  $.ajax({
    url: url,
    type: 'GET',
    success: function(res) {
      $('#modalBody').html(res.data ?? '<p class="text-muted">Không có dữ liệu</p>');
    },
    error: function(err) {
      $('#modalBody').html('<p class="text-danger">Không thể tải dữ liệu</p>');
    }
  });
}
</script>
@endsection
