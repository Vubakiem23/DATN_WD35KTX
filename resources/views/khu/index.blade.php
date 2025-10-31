@extends('admin.layouts.admin')
@section('title','Quản lý khu')

@section('content')
<div class="container">
  @push('styles')
  <style>
    .khu-actions .btn-action{width:40px;height:36px;display:inline-flex;align-items:center;justify-content:center;border-radius:10px}
    .khu-actions .btn-action i{font-size:14px}
  </style>
  @endpush
  


  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Quản lý khu</h5>
      <a href="{{ route('khu.create') }}" class="btn btn-outline-primary">Tạo khu</a>
    </div>
    <div class="card-body p-0">
      <table class="table mb-0 align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>Tên khu</th>
            <th>Giới tính</th>
            <th>Mô tả</th>
            <th class="text-end">Hành động</th>
          </tr>
        </thead>
        <tbody>
          @forelse($khus as $khu)
          <tr>
            <td>{{ $khu->id }}</td>
            <td>{{ $khu->ten_khu }}</td>
            <td>{{ $khu->gioi_tinh }}</td>
            <td>{{ $khu->mo_ta }}</td>
            <td class="text-end khu-actions">
              <a href="{{ route('khu.show', $khu) }}" class="btn btn-outline-info btn-action" title="Chi tiết"><i class="fa fa-eye"></i></a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center p-4 text-muted">Chưa có khu nào</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection


