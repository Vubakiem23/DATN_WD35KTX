@extends('public.layouts.app')
@section('title', 'Chi tiết phản hồi sinh viên')
@section('content')
    <div class="content-section about-page">
        <div class="container">
            <h3 class="title">
                Chi tiết phản hồi: #{{ $phanHoi->id}} - {{ $phanHoi->tieu_de }}
            </h3>

            <div class="mt-3">
                <form method="POST" action="{{ route('client.phan_hoi.update', $phanHoi) }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="tieu_de">Tiêu đề</label>
                        <input type="text" class="form-control" id="tieu_de" placeholder="Vui lòng nhập..."
                               value="{{ $phanHoi->tieu_de }}" name="tieu_de" required>
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
    </div>
@endsection
