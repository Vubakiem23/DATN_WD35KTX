@extends('admin.layouts.admin')

@section('title', '➕ Thêm Hashtag')

@section('content')
<div class="container mt-4" style="max-width: 700px;">
    
    {{-- Tiêu đề trang --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">➕ Thêm Hashtag</h3>
        <p class="text-muted fs-6 mb-0">Tạo mới hashtag để sử dụng trong hệ thống.</p>
    </div>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm rounded-pill px-4 py-2">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form thêm hashtag --}}
    <div class="room-table-wrapper mt-3">
        <form action="{{ route('hashtags.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="ten" class="form-label fw-semibold">Tên Hashtag</label>
                <input type="text" id="ten" name="ten" class="form-control shadow-sm"
                       value="{{ old('ten') }}" required
                       placeholder="Nhập tên hashtag...">
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="submit" class="btn btn-dergin btn-dergin--info px-4">
                    💾 Lưu
                </button>
                <a href="{{ route('hashtags.index') }}" class="btn btn-dergin btn-dergin--danger px-4">
                    ⬅️ Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

{{-- CSS đồng bộ --}}
@push('styles')
<style>
    .room-page__title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
    }

    .room-table-wrapper {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
        padding: 2rem;
    }

    .btn-dergin {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .45rem 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: .85rem;
        border: none;
        color: #fff;
        background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
        box-shadow: 0 6px 14px rgba(78, 84, 200, .25);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .btn-dergin:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 20px rgba(78, 84, 200, .35);
        color: #fff;
    }

    .btn-dergin--info {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    }

    .btn-dergin--danger {
        background: linear-gradient(135deg, #f43f5e 0%, #ef4444 100%);
    }
</style>
@endpush
@endsection
