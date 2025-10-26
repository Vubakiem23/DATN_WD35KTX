@extends('admin.layouts.admin')

@section('title', 'Danh sách thông báo')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="container mt-4">
    <h3 class="page-title">📢 Danh sách thông báo</h3>

    {{-- Ô tìm kiếm (tùy chọn, giống trang sự cố) --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm (tiêu đề, nội dung, phòng, khu, đối tượng)">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (!empty(request('search')))
                <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
            @endif
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách các thông báo</h4>
        <a href="{{ route('thongbao.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm thông báo</a>
    </div>

    {{-- Thông báo --}}
    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- Lưới thẻ --}}
    <div class="tab-content">
        <div class="row g-3">
            @forelse($thongbaos as $tb)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">

                        {{-- Header --}}
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>{{ Str::limit($tb->tieu_de, 40) }}</strong>
                            <span class="text-muted">#{{ $tb->id }}</span>
                        </div>

                        {{-- Ảnh thông báo / placeholder --}}
                        @if ($tb->anh)
                            <img src="{{ asset('storage/' . $tb->anh) }}" class="card-img-top"
                                style="height:160px;object-fit:cover" alt="Ảnh thông báo #{{ $tb->id }}">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center"
                                style="height:160px;background:#f8f9fa">
                                <svg width="80" height="60" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" aria-label="no image">
                                    <rect width="24" height="24" rx="2" fill="#e9ecef" />
                                    <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        @endif

                        {{-- Nội dung --}}
                        <div class="card-body">
                            <p class="mb-1"><strong>Ngày đăng:</strong>
                                {{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}
                            </p>

                            <p class="mb-1"><strong>Đối tượng:</strong> {{ $tb->doi_tuong ?? '---' }}</p>
                            <p class="mb-1"><strong>Phòng:</strong> {{ $tb->phong->ten_phong ?? '---' }}</p>
                            <p class="mb-1"><strong>Khu:</strong>
                                {{ $tb->phong->khu ?? '---' }}
                            </p>

                            <p class="mb-0"><strong>Nội dung:</strong> {{ Str::limit($tb->noi_dung, 100) }}</p>
                        </div>

                        {{-- Footer: hành động --}}
                        <div class="card-footer d-flex gap-2">
                            <a href="{{ route('thongbao.show', $tb->id) }}" class="btn btn-sm btn-secondary flex-fill">Xem</a>
                            <a href="{{ route('thongbao.edit', $tb->id) }}" class="btn btn-sm btn-warning flex-fill">Sửa</a>
                            <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST"
                                style="display:inline-block" class="mb-0 flex-fill">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger w-100"
                                    onclick="return confirm('Bạn chắc chắn muốn xóa thông báo này?')">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body text-center text-muted py-4">
                            <i class="fa fa-exclamation-circle"></i> Chưa có thông báo nào.
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $thongbaos->onEachSide(1)->links() }}
    </div>
</div>

@push('styles')
<style>
    .badge {
        border-radius: 10rem;
        padding: .35rem .6rem;
        font-weight: 600
    }
</style>
@endpush
@endsection
