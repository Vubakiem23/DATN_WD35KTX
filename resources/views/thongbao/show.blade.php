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

            {{-- Phòng và Khu --}}
            <p><strong>Phòng:</strong> {{ $thongbao->phong->ten_phong ?? 'Chưa có phòng' }}</p>
            <p><strong>Khu:</strong> 
                @if($thongbao->phong)
                    {{ $thongbao->phong->khu }}
                @else
                    <span class="text-danger">Chưa có khu</span>
                @endif
            </p>

            {{-- Ảnh --}}
            <p><strong>Ảnh:</strong></p>
            @if($thongbao->anh)
                <img src="{{ asset('storage/' . $thongbao->anh) }}" alt="Ảnh thông báo" class="img-fluid" style="max-width: 300px;">
            @else
                <span>Không có ảnh</span>
            @endif

            <hr>
            <p><strong>Nội dung:</strong></p>
            <p>{{ $thongbao->noi_dung }}</p>
        </div>
    </div>

    <a href="{{ route('thongbao.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
</div>
@endsection
