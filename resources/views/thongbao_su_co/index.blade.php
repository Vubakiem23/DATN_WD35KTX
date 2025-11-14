@extends('admin.layouts.admin')

@section('title', 'Danh sách thông báo sự cố')

@section('content')
<div class="container mt-4">

    {{-- Tiêu đề --}}
    <div class="mb-4">
        <h3 class="room-page__title mb-2">📢 Danh sách thông báo sự cố</h3>
        <p class="text-muted fs-6 mb-0">Theo dõi các sự cố được sinh viên báo cáo và tình trạng xử lý.</p>
    </div>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success mt-3 shadow-sm rounded-pill px-4 py-2">{{ session('success') }}</div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper mt-4">
        <div class="table-responsive">
            @php
                $sttBase = ($thongbaos->currentPage() - 1) * $thongbaos->perPage();
            @endphp

            <table class="table table-hover mb-0 room-table">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th class="fit">Nội dung</th>
                        <th class="fit text-center">Ảnh</th>
                        <th class="fit text-center">Ngày đăng</th>
                        <th class="fit">Phòng</th>
                        
                        <th class="fit">Sinh viên</th>
                        <th class="fit text-center">Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thongbaos as $tb)
                        @php
                            $stt = $sttBase + $loop->iteration;
                            $suCo = $tb->su_co;
                            $sv = $suCo?->sinh_vien;
                            $trangThai = $suCo?->trang_thai ?? '---';

                            $badgeClass = match($trangThai) {
                                'Tiếp nhận' => 'badge-soft-secondary',
                                'Đang xử lý' => 'badge-soft-warning',
                                'Hoàn thành' => 'badge-soft-success',
                                default => 'badge-soft-secondary',
                            };
                        @endphp
                        <tr>
                            <td class="text-center">{{ $stt }}</td>
                            <td>{{ \Illuminate\Support\Str::limit(strip_tags($tb->noi_dung ?? ''), 60, '...') }}</td>

                            {{-- Ảnh --}}
                            <td class="text-center">
                                @php
                                    $anh = optional($suCo)->anh;
                                    $imgSrc = null;

                                    if($anh) {
                                        // Kiểm tra nếu là base64 (BLOB) - có thể thêm prefix 'data:image/jpeg;base64,' trong DB
                                        if(str_starts_with($anh, '/9') || str_starts_with($anh, 'iVB')) {
                                            $imgSrc = 'data:image/jpeg;base64,' . base64_encode($anh);
                                        } else {
                                            // Ngược lại coi như là đường dẫn
                                            $imgSrc = asset($anh);
                                        }
                                    }
                                @endphp

                                @if($imgSrc)
                                    <img src="{{ $imgSrc }}" class="rounded" style="height:60px;width:60px;object-fit:cover;">
                                @else
                                    <div class="no-img-box">—</div>
                                @endif
                            </td>

                            <td class="text-center">{{ \Carbon\Carbon::parse($tb->ngay_tao)->format('d/m/Y H:i') }}</td>
                            <td>{{ $suCo?->phong?->ten_phong ?? '---' }}</td>
                            
                            <td>
                                <div class="fw-semibold">{{ $sv?->ho_ten ?? '---' }}</div>
                                <small class="text-muted">{{ $sv?->ma_sinh_vien ?? '' }}</small>
                            </td>
                            <td class="text-center"><span class="badge {{ $badgeClass }}">{{ $trangThai }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2" alt="">
                                <div>Chưa có thông báo sự cố nào</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $thongbaos->onEachSide(1)->links() }}
    </div>
</div>

@push('styles')
<style>
    html { scroll-behavior: auto !important; }
    .room-page__title { font-size: 1.8rem; font-weight: 700; color: #1f2937; }
    .room-table-wrapper { background:#fff; border-radius:14px; box-shadow:0 10px 30px rgba(15,23,42,.06); padding:1.5rem; }
    .room-table { border-collapse: separate; border-spacing: 0 12px; }
    .room-table thead th { font-size:.8rem; text-transform:uppercase; color:#6c757d; border:none; padding-bottom:.75rem; }
    .room-table tbody tr { background:#f9fafc; border-radius:16px; transition: transform .2s ease, box-shadow .2s ease; }
    .room-table tbody tr:hover { transform:translateY(-2px); box-shadow:0 10px 25px rgba(15,23,42,.08); }
    .room-table tbody td { border:none; vertical-align:middle; padding:1rem .95rem; }
    .room-table tbody tr td:first-child { border-top-left-radius:16px; border-bottom-left-radius:16px; }
    .room-table tbody tr td:last-child { border-top-right-radius:16px; border-bottom-right-radius:16px; }
    .no-img-box { height:60px; width:60px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; border-radius:8px; color:#94a3b8; font-size:1rem; }
    .badge-soft-success { background:rgba(34,197,94,.15); color:#16a34a; font-weight:600; border-radius:30px; padding:.4em .75em; }
    .badge-soft-warning { background:rgba(250,204,21,.15); color:#ca8a04; font-weight:600; border-radius:30px; padding:.4em .75em; }
    .badge-soft-secondary { background:rgba(107,114,128,.15); color:#374151; font-weight:600; border-radius:30px; padding:.4em .75em; }
    @media(max-width:992px) {
        .room-table thead { display:none; }
        .room-table tbody { display:block; }
        .room-table tbody tr { display:flex; flex-direction:column; padding:1rem; }
        .room-table tbody td { display:flex; justify-content:space-between; padding:.35rem 0; }
    }
</style>
@endpush
@endsection
