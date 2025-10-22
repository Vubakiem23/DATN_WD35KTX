<div class="p-3 bg-light rounded-3">
    <h4 class="fw-bold text-primary mb-3 text-center">
        <i class="bi bi-box-seam me-2"></i>{{ $taiSan->khoTaiSan->ten_tai_san ?? 'Không xác định' }}
    </h4>

    <div class="row g-3">
        <div class="col-md-6">
            <p><strong>Mã tài sản:</strong> {{ $taiSan->khoTaiSan->ma_tai_san ?? '-' }}</p>
            <p><strong>Phòng:</strong> {{ $taiSan->phong->ten_phong ?? '-' }}</p>
            <p><strong>Số lượng:</strong> {{ $taiSan->so_luong }}</p>
        </div>

        <div class="col-md-6">
            <p><strong>Tình trạng ban đầu:</strong> {{ ucfirst($taiSan->tinh_trang ?? '-') }}</p>
            <p><strong>Tình trạng hiện tại:</strong> {{ ucfirst($taiSan->tinh_trang_hien_tai ?? '-') }}</p>
            <p><strong>Ghi chú:</strong> {{ $taiSan->ghi_chu ?? '-' }}</p>
        </div>
    </div>

    @if(!empty($taiSan->khoTaiSan->hinh_anh))
        <div class="text-center mt-4">
            <img src="{{ asset('uploads/kho/' . $taiSan->khoTaiSan->hinh_anh) }}" 
                 alt="Ảnh tài sản"
                 class="img-fluid rounded-3 shadow-sm border border-2 border-info"
                 style="max-height:300px; transition:transform .3s;"
                 onmouseover="this.style.transform='scale(1.05)'"
                 onmouseout="this.style.transform='scale(1)'">
            <p class="text-muted mt-2"><em>Ảnh minh họa tài sản</em></p>
        </div>
    @endif
</div>
