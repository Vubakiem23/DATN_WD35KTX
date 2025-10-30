@extends('admin.layouts.admin')
@section('title', 'Kho đồ')

@section('content')
<div class="container">
    <h3 class="mb-4">📦 Kho tài sản</h3>

    <div class="row">
        @foreach($loaiTaiSan as $loai)
        <div class="col-md-3 mb-3">
            <div class="card shadow-sm text-center">
                @if($loai->hinh_anh)
                <img src="{{ asset('uploads/loai/'.$loai->hinh_anh) }}" class="card-img-top" style="height:150px;object-fit:cover;">
                @endif
                <div class="card-body">
                    <h5 class="card-title">{{ $loai->ten_loai }}</h5>
                    <p class="small text-muted">
                        Số lượng trong kho: {{ $loai->kho_tai_san_sum_so_luong ?? 0 }}
                    </p>
                    <a href="{{ route('kho.related', $loai->id) }}" class="btn btn-primary btn-sm">Xem tài sản</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">
    {{ $loaiTaiSan->links('pagination::bootstrap-5') }}
</div>

</div>
@endsection