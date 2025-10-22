@extends('admin.layouts.admin')

@section('content')
    @php use Illuminate\Support\Str; @endphp

    <div class="container mt-4">
        <h3 class="page-title">🧯 Danh sách sự cố</h3>

        {{-- Ô tìm kiếm (giữ giống trang sinh viên) --}}
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                    placeholder="Tìm kiếm (MSSV, họ tên, phòng, mô tả, trạng thái)">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                @if (!empty(request('search')))
                    <a href="{{ route('suco.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Danh sách các sự cố</h4>
            <a href="{{ route('suco.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm sự cố</a>
        </div>

        {{-- Thông báo --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
        @endif

        {{-- Lưới thẻ giống trang Sinh viên --}}
        <div class="tab-content">
            <div class="row g-3">
                @forelse($suco as $sc)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            {{-- Header: tiêu đề + id --}}
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>
                                    {{ $sc->sinhVien->ho_ten ?? 'Không rõ sinh viên' }}
                                </strong>
                                <span class="font-weight-bold">#{{ $sc->id }}</span>
                            </div>

                            {{-- Ảnh sự cố / placeholder --}}
                            @if (!empty($sc->anh))
                                <img src="{{ asset($sc->anh) }}" class="card-img-top" style="height:160px;object-fit:cover"
                                    alt="Ảnh sự cố #{{ $sc->id }}">
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

                            {{-- Nội dung thẻ --}}
                            <div class="card-body">
                                <p class="mb-1"><strong>Sinh viên:</strong>
                                    {{ $sc->sinhVien->ho_ten ?? '---' }}
                                    @if (!empty($sc->sinhVien?->ma_sinh_vien))
                                        <small class="text-muted">({{ $sc->sinhVien->ma_sinh_vien }})</small>
                                    @endif
                                </p>
                                <p class="mb-1"><strong>Phòng:</strong> {{ $sc->phong->ten_phong ?? '---' }}</p>
                                <p class="mb-1"><strong>Ngày gửi:</strong>
                                    {{ !empty($sc->ngay_gui) ? \Carbon\Carbon::parse($sc->ngay_gui)->format('d/m/Y') : '-' }}
                                </p>

                                @php
                                    $status = $sc->trang_thai ?? 'Khác';
                                    $badge = match ($status) {
                                        'Tiếp nhận' => 'bg-secondary',
                                        'Đang xử lý' => 'bg-warning',
                                        'Đã xử lý' => 'bg-warning',
                                        'Hoàn thành' => 'bg-success',
                                        default => 'bg-info',
                                    };
                                @endphp
                                <p class="mb-1"><strong>Trạng thái:</strong>
                                    <span class="badge {{ $badge }}">{{ $status }}</span>
                                </p>

                                <p class="mb-0"><strong>Mô tả:</strong> {{ Str::limit($sc->mo_ta, 120) }}</p>
                            </div>

                            {{-- Footer: các nút hành động giống bố cục trang Sinh viên --}}
                            <div class="card-footer d-flex gap-2">
                                <a href="{{ route('suco.show', $sc->id) }}" class="btn btn-sm flex-fill btn-secondary">Chi
                                    tiết</a>

                                <a href="{{ route('suco.edit', $sc->id) }}"
                                    class="btn btn-sm btn-warning flex-fill">Sửa</a>

                                <form action="{{ route('suco.destroy', $sc->id) }}" method="POST"
                                    style="display:inline-block" class="mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger flex-fill"
                                        onclick="return confirm('Xác nhận xóa sự cố này?')">
                                        Xóa
                                    </button>
                                </form>

                                {{-- nếu cần thêm nút đổi trạng thái có thể đặt ở đây --}}
                                {{-- <form ...>Duyệt/Xử lý</form> --}}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-body text-center text-muted py-4">
                                <i class="fa fa-exclamation-circle"></i> Chưa có sự cố nào được ghi nhận.
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $suco->onEachSide(1)->links() }}
        </div>
    </div>

    @push('styles')
        <style>
            /* Đồng bộ nhẹ để giống trang Sinh viên */
            .badge {
                border-radius: 10rem;
                padding: .35rem .6rem;
                font-weight: 600
            }
        </style>
    @endpush
@endsection
