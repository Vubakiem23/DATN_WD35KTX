@extends('admin.layouts.admin')

@section('title', 'Thông báo sinh viên vào phòng')

@section('content')
<div class="container mt-4">

    <h3 class="room-page__title mb-2">📢 Thông báo sinh viên đã vào phòng</h3>
    <p class="text-muted fs-6 mb-4">Danh sách các thông báo khi sinh viên được thêm vào phòng.</p>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success mt-3 shadow-sm rounded-pill px-4 py-2">
            {{ session('success') }}
        </div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="room-table-wrapper mt-4">
        <div class="table-responsive">
            @php
                $perPage = $thongbaos->perPage();
                $currentPage = $thongbaos->currentPage();
                $sttBase = ($currentPage - 1) * $perPage;
            @endphp

            <table class="table table-hover mb-0 room-table">
                <thead>
                    <tr>
                        <th class="fit text-center">STT</th>
                        <th class="fit">Sinh viên</th>
                        <th class="fit">Phòng</th>
                        <th class="fit">Nội dung</th>
                        <th class="fit text-center">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($thongbaos as $tb)
                        @php
                            $stt = $sttBase + $loop->iteration;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $stt }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if(optional($tb->sinhVien)->anh_sinh_vien)
                                        <img src="{{ asset('storage/' . $tb->sinhVien->anh_sinh_vien) }}" 
                                             class="rounded-circle me-2" 
                                             width="40" height="40" 
                                             alt="{{ optional($tb->sinhVien)->ho_ten }}">
                                    @endif
                                    <div>
                                        <div class="fw-semibold">{{ optional($tb->sinhVien)->ho_ten ?? '---' }}</div>
                                        <small class="text-muted">{{ optional($tb->sinhVien)->ma_sinh_vien }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ optional($tb->phong)->ten_phong ?? '---' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($tb->noi_dung ?? '', 60, '...') }}</td>
                            <td class="text-center">
                                {{ optional($tb->created_at)->format('d/m/Y H:i') ?? '---' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2" alt="">
                                <div>Chưa có thông báo nào</div>
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
    .room-page__title { font-size: 1.8rem; font-weight: 700; color: #1f2937; }
    .room-table-wrapper { background: #fff; border-radius: 14px; box-shadow: 0 10px 30px rgba(15,23,42,0.06); padding: 1.5rem; }
    .room-table { border-collapse: separate; border-spacing: 0 12px; margin-bottom:0; }
    .room-table thead th { font-size:0.8rem; text-transform:uppercase; color:#6c757d; border:none; padding-bottom:0.75rem; }
    .room-table tbody tr { background:#f9fafc; border-radius:16px; transition: transform 0.2s, box-shadow 0.2s; }
    .room-table tbody tr:hover { transform:translateY(-2px); box-shadow:0 12px 30px rgba(15,23,42,0.08); }
    .room-table tbody td { border:none; vertical-align:middle; padding:1rem 0.95rem; }
    .badge-soft-success { background: rgba(34,197,94,0.15); color:#16a34a; font-weight:600; padding:0.45em 0.75em; border-radius:30px; }
    .room-table tbody img { object-fit: cover; }
</style>
@endpush
@endsection
