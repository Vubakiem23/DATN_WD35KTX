@extends('admin.layouts.admin') 

@section('title','Gán sinh viên vào phòng')

@section('content')
<div class="container">
  <h3>Gán: {{ $sv->ho_ten }} ({{ $sv->ma_sinh_vien }})</h3>

  <form action="{{ route('assign.do', $sv->id) }}" method="POST">
    @csrf
    <div class="mb-3">
      <label>Chọn phòng</label>
      <select name="phong_id" class="form-control" required>
        @foreach($phongs as $p)
        <option value="{{ $p->id }}">{{ $p->ten_phong }} - {{ $p->khu }} ({{ $p->trang_thai }}) - {{ $p->gioi_tinh ?? 'Tự do' }} - {{ $p->suc_chua }} chỗ</option>
        @endforeach
      </select>
    </div>

    <button class="btn btn-primary">Gán</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Quay lại</a>
  </form>
</div>
@endsection
