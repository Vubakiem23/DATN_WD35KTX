@extends('public.layouts.app')

@section('title', 'Tin tức | Ký túc xá VaMos')

@section('content')
<div class="container py-5">
    <h2 class="mb-4">TIN TỨC</h2>

    @if($tintucs->count() > 0)
        <div class="row">
            @foreach($tintucs as $tin)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <a href="{{ route('public.tintuc.show', $tin->slug) }}">
                        <img src="{{ asset($tin->hinh_anh) }}" class="card-img-top" style="height:200px; object-fit:cover;">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('public.tintuc.show', $tin->slug) }}">{{ $tin->tieu_de }}</a>
                        </h5>
                        <p class="text-muted small">{{ \Carbon\Carbon::parse($tin->ngay_tao)->format('d/m/Y') }}</p>
                        <p>{{ Str::limit(strip_tags($tin->noi_dung), 120) }}</p>
                        <a href="{{ route('public.tintuc.show', $tin->slug) }}">Đọc tiếp →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $tintucs->links('pagination::bootstrap-5') }}
        </div>
    @else
        <p>Hiện chưa có tin tức nào.</p>
    @endif
</div>
@endsection
