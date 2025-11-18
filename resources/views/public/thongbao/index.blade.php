@extends('public.layouts.app')

@section('title', 'Thông báo | Ký túc xá VaMos')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">THÔNG BÁO</h2>

    @if($thongbaos->count() > 0)
        <div class="list-group">
            @foreach($thongbaos as $tb)
    <a href="{{ route('public.thongbao.show', $tb->id) }}" class="list-group-item list-group-item-action mb-3">

        <div class="d-flex align-items-start"> 
            
            <!-- Ảnh bên trái -->
            <div class="me-3">
                <img src="{{ Storage::url($tb->anh) }}" 
                     class="news-thumbnail"
                     alt="{{ $tb->tieu_de }}"
                     style="width: 150px; height: 120px; object-fit: cover; border-radius: 8px;">
            </div>

            <!-- Nội dung bên phải -->
            <div class="flex-grow-1">
                <div class="d-flex w-100 justify-content-between">
                    <h5 class="mb-1">{{ $tb->tieuDe->ten_tieu_de ?? 'Không có tiêu đề' }}</h5>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}</small>
                </div>

                <p class="mb-1">{{ Str::limit(strip_tags($tb->noi_dung), 90) }}</p>

                @if($tb->khus->count() > 0)
                    <small>Khối: {{ $tb->khus->pluck('ten_khu')->join(', ') }}</small>
                @endif
            </div>

        </div>

    </a>
@endforeach

        </div>

        <div class="mt-4">
            {{ $thongbaos->links('pagination::bootstrap-5') }}
        </div>
    @else
        <p>Hiện chưa có thông báo nào.</p>
    @endif
</div>
@endsection
