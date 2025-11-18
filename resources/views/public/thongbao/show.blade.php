@extends('public.layouts.app')

@section('title', $thongbao->tieuDe->ten_tieu_de . ' | Thông báo')

@section('content')
<div class="content-section">
    <div class="container">

        <div class="panel p-4">

            <h2 class="mb-3">{{ $thongbao->tieuDe->ten_tieu_de ?? 'Thông báo' }}</h2>

            <p class="text-muted mb-3">
                <i class="far fa-calendar-alt"></i>
                {{ \Carbon\Carbon::parse($thongbao->ngay_dang)->format('d/m/Y') }}

                @if($thongbao->khus->count() > 0)
                • Khu:
                @foreach($thongbao->khus as $khu)
                <span class="badge bg-primary">{{ $khu->ten_khu }}</span>
                @endforeach
                @endif
            </p>

            <!-- @if($thongbao->anh)
            <div class="mb-4 text-center">
                <img src="{{ Storage::url($thongbao->anh) }}" class="news-thumbnail" alt="{{ $thongbao->tieu_de }}" style="width: 100%; max-width: 800px; max-height: 1300px; border-radius: 10px;">
            </div>
            @endif -->


            <div class="news-detail-content ">
                {!! $thongbao->noi_dung !!}
            </div>

            @if($thongbao->file)
            <div class="mt-3">
                <a href="{{ Storage::url($thongbao->file) }}" target="_blank" class="btn btn-primary">
                    Tải tài liệu đính kèm
                </a>
            </div>
            @endif

            <a href="{{ route('public.thongbao.index') }}" class="view-more-link mt-4 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>

        </div>

    </div>
</div>
@endsection