@extends('admin.layouts.admin')

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Chi tiết thông báo</h3>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">{{ $thongbao->tieu_de }}</h4>
            <p><strong>Ngày đăng:</strong> {{ \Carbon\Carbon::parse($thongbao->ngay_dang)->format('d/m/Y') }}</p>
            <p><strong>Đối tượng:</strong> {{ $thongbao->doi_tuong }}</p>
            <hr>
            <p><strong>Nội dung:</strong></p>
            <p>{{ $thongbao->noi_dung }}</p>
        </div>
    </div>

    <a href="{{ route('thongbao.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
</div>
@endsection
