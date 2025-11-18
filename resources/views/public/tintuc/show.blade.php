@extends('public.layouts.app')

@section('title', $tinTuc->tieu_de . ' | Tin tức')

@section('content')
<div class="content-section">
    <div class="container">

        <div class="panel p-4">

            <h2 class="mb-3">{{ $tinTuc->tieu_de }}</h2>

            <p class="text-muted mb-3">
                <i class="far fa-calendar-alt"></i>
                {{ \Carbon\Carbon::parse($tinTuc->ngay_tao)->format('d/m/Y') }}

                @if($tinTuc->hashtags->count() > 0)
                • Hashtags:
                @foreach($tinTuc->hashtags as $tag)
                <span class="badge bg-primary">#{{ $tag->ten }}</span>
                @endforeach
                @endif

            </p>
            <div class="news-detail-content">
                {!! $tinTuc->noi_dung !!}
            </div>

            <a href="{{ route('public.tintuc.index') }}" class="view-more-link mt-4 d-inline-block">
                <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
            </a>


        </div>

    </div>
</div>
@endsection