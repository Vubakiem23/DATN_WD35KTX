<div class="p-3 bg-light rounded-3">
    {{-- Tiêu đề tài sản --}}
    <div class="text-center mb-3">
        <h4 class="fw-bold text-primary">
            <i class="bi bi-gear-fill me-2"></i>{{ $lich->taiSan->ten_tai_san ?? 'Không xác định' }}
        </h4>
    </div>

    {{-- Thông tin chi tiết --}}
    <div class="row g-3 text-center ">
        <div class="col-md-6">
            <p><strong>🗓️ Ngày bảo trì:</strong> {{ $lich->ngay_bao_tri }}</p>
            <p><strong>✅ Ngày hoàn thành:</strong> {{ $lich->ngay_hoan_thanh ?? '-' }}</p>
        </div>

        <div class="col-md-6">
            <p><strong>🧾 Mô tả:</strong> {{ $lich->mo_ta ?? '-' }}</p>
            <p>
                <strong>⚙️ Trạng thái:</strong>
                <span class="badge fs-6
                    @if($lich->trang_thai == 'Hoàn thành') bg-success
                    @elseif($lich->trang_thai == 'Đang bảo trì') bg-warning text-dark
                    @else bg-secondary @endif">
                    {{ $lich->trang_thai }}
                </span>
            </p>
        </div>
    </div>

    {{-- Hình ảnh bảo trì --}}
    @if($lich->hinh_anh)
        <div class="text-center mt-4">
            <img src="{{ asset('uploads/lichbaotri/'.$lich->hinh_anh) }}"
                 alt="Ảnh bảo trì"
                 class="img-fluid rounded-3 shadow-sm border border-2 border-primary"
                 style="max-height: 300px; transition: transform 0.3s ease;"
                 onmouseover="this.style.transform='scale(1.05)'"
                 onmouseout="this.style.transform='scale(1)'">
            <p class="text-muted mt-2"><em>Ảnh minh họa quá trình bảo trì</em></p>
        </div>
    @endif
</div>
