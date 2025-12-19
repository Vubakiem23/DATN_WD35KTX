@extends('admin.layouts.admin')
@section('title', 'Chi tiết phản hồi sinh viên')
@section('content')
    <div class="container">
        <h3 class="title">
            Chi tiết phản hồi: #{{ $phanHoi->id}} - {{ $phanHoi->tieu_de }}
        </h3>

        <div class="mt-3">
            <form method="POST" action="{{ route('admin.phan_hoi.update', $phanHoi) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="form-group col-md-8">
                        <label for="tieu_de">Tiêu đề</label>
                        <input type="text" class="form-control" id="tieu_de" placeholder="Vui lòng nhập..."
                               value="{{ $phanHoi->tieu_de }}" name="tieu_de" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="trang_thai">Trạng thái</label>
                        <select name="trang_thai" id="trang_thai" class="form-control">
                            <option {{ $phanHoi->trang_thai == 1 ? 'selected' : '' }} value="1">Đã xác nhận</option>
                            <option {{ $phanHoi->trang_thai == 0 ? 'selected' : '' }} value="0">Chờ xác nhận</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="noi_dung">Nội dung</label>
                    <textarea class="form-control" name="noi_dung" id="noi_dung" rows="10"
                              required>{{ $phanHoi->noi_dung }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-2">Lưu thay đổi</button>
            </form>
        </div>
    </div>
@endsection
