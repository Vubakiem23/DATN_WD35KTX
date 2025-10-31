<div class="p-4 bg-light rounded-3 shadow-sm border border-2 border-info">
    {{-- 🔹 Tiêu đề --}}
    <h4 class="fw-bold text-primary mb-4 text-center">
        <i class="bi bi-box-seam me-2"></i>{{ $taiSan->khoTaiSan->ten_tai_san ?? 'Không xác định' }}
    </h4>

    {{-- 🔸 Thông tin chi tiết --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="border rounded-3 bg-white p-3 h-100 shadow-sm">
                <h6 class="text-secondary fw-bold mb-2">Thông tin cơ bản</h6>
                <p><strong>Mã tài sản:</strong> {{ $taiSan->khoTaiSan->ma_tai_san ?? '-' }}</p>
                <p><strong>Phòng:</strong> {{ $taiSan->phong->ten_phong ?? '-' }}</p>
                <p><strong>Số lượng:</strong> {{ $taiSan->so_luong }}</p>
                <p><strong>Ghi chú:</strong> {{ $taiSan->ghi_chu ?? '-' }}</p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded-3 bg-white p-3 h-100 shadow-sm">
                <h6 class="text-secondary fw-bold mb-2">Tình trạng</h6>
                <p>
                    <strong>Ban đầu:</strong> 
                    <span class="badge 
                        @if($taiSan->tinh_trang == 'mới') bg-success
                        @elseif($taiSan->tinh_trang == 'cũ') bg-secondary
                        @elseif($taiSan->tinh_trang == 'bảo trì') bg-warning text-dark
                        @elseif($taiSan->tinh_trang == 'hỏng') bg-danger
                        @else bg-light @endif">
                        {{ ucfirst($taiSan->tinh_trang ?? '-') }}
                    </span>
                </p>
                <p>
                    <strong>Hiện tại:</strong> 
                    <span class="badge 
                        @if($taiSan->tinh_trang_hien_tai == 'mới') bg-success
                        @elseif($taiSan->tinh_trang_hien_tai == 'cũ') bg-secondary
                        @elseif($taiSan->tinh_trang_hien_tai == 'bảo trì') bg-warning text-dark
                        @elseif($taiSan->tinh_trang_hien_tai == 'hỏng') bg-danger
                        @else bg-light @endif">
                        {{ ucfirst($taiSan->tinh_trang_hien_tai ?? '-') }}
                    </span>
                </p>
            </div>
        </div>
    </div>

    {{-- 👨‍🎓 Sinh viên đang sử dụng (Căn giữa) --}}
    <div class="mt-5 p-4 bg-white border rounded-3 shadow-sm text-center">
        <h5 class="text-secondary fw-bold mb-4">
            <i class="bi bi-person-circle me-2"></i>Sinh viên đang sử dụng
        </h5>

        @if($taiSan->slots && $taiSan->slots->isNotEmpty())
            @php
                $sv = $taiSan->slots->first()->sinhVien ?? null;
            @endphp
            @if($sv)
                <div class="d-flex flex-column align-items-center">
                    <img src="{{ $sv->anh_sinh_vien ? asset('storage/' . $sv->anh_sinh_vien) : asset('images/default-avatar.png') }}"
                         class="rounded-circle border border-3 border-primary shadow-sm mb-3"
                         alt="Ảnh sinh viên"
                         width="90" height="90"
                         style="object-fit: cover;">
                    <div>
                        <p class="mb-1"><strong>👤 Họ tên:</strong> {{ $sv->ho_ten }}</p>
                        <p class="mb-1"><strong>🎓 Mã SV:</strong> {{ $sv->ma_sinh_vien ?? '—' }}</p>
                        <p class="mb-0"><strong>📧 Email:</strong> {{ $sv->email ?? '—' }}</p>
                    </div>
                </div>
            @else
                <p class="text-muted fst-italic">Chưa có sinh viên nào sử dụng tài sản này.</p>
            @endif
        @else
            <p class="text-muted fst-italic">Chưa có sinh viên nào sử dụng tài sản này.</p>
        @endif
    </div>

    {{-- 🖼 Ảnh minh họa --}}
    @if(!empty($taiSan->khoTaiSan->hinh_anh))
        <div class="text-center mt-4">
            <img src="{{ asset('storage/' . $taiSan->khoTaiSan->hinh_anh) }}" 
                 alt="Ảnh tài sản"
                 class="img-fluid rounded-3 shadow-sm border border-2 border-info"
                 style="max-height:300px; transition:transform .3s;"
                 onmouseover="this.style.transform='scale(1.05)'"
                 onmouseout="this.style.transform='scale(1)'">
            <p class="text-muted mt-2"><em>Ảnh minh họa tài sản</em></p>
        </div>
    @endif
</div>
