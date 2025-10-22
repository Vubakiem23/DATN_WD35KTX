<div class="modal fade" id="modalKho" tabindex="-1" aria-labelledby="modalKhoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            {{-- Header --}}
            <div class="modal-header bg-gradient bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center" id="modalKhoLabel">
                    <i class="bi bi-box-seam me-2"></i> Chi tiết tài sản trong kho
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            {{-- Body --}}
            <div class="modal-body bg-light">
                <div class="p-3">
                    <div class="mb-3 text-center">
                        <h4 class="fw-bold text-primary">{{ $taiSan->ten_tai_san }}</h4>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <p><strong>📋 Mã tài sản:</strong> {{ $taiSan->ma_tai_san }}</p>
                            <p><strong>📏 Đơn vị tính:</strong> {{ $taiSan->don_vi_tinh ?? '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>🔢 Số lượng:</strong> {{ $taiSan->so_luong }}</p>
                            <p><strong>🧾 Ghi chú:</strong> {{ $taiSan->ghi_chu ?? '-' }}</p>
                        </div>
                    </div>

                    @if($taiSan->hinh_anh)
                        <div class="text-center mt-4">
                            <img src="{{ asset('uploads/kho/'.$taiSan->hinh_anh) }}"
                                 class="img-fluid rounded-3 shadow-sm border border-2 border-primary"
                                 style="max-height: 300px; transition: transform 0.3s ease;"
                                 onmouseover="this.style.transform='scale(1.05)'"
                                 onmouseout="this.style.transform='scale(1)'"
                                 alt="Ảnh tài sản">
                            <p class="text-muted mt-2"><em>Ảnh minh họa tài sản</em></p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>
