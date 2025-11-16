@extends('client.layouts.app')

@section('title', 'Báo sự cố - Sinh viên')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">
            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
            Báo sự cố phòng
        </h2>
    </div>
</div>

@if(!$sinhVien)
    {{-- Chưa nộp hồ sơ --}}
    <div class="row">
        <div class="col-12">
            <div class="card text-center py-5">
                <i class="fas fa-file-alt fa-4x text-info mb-3"></i>
                <h4 class="text-info">Bạn chưa nộp hồ sơ đăng ký ký túc xá</h4>
                <p class="text-muted">Vui lòng nộp hồ sơ để có thể báo sự cố.</p>
            </div>
        </div>
    </div>

@elseif(!$phong)
    {{-- Chưa có phòng --}}
    <div class="row">
        <div class="col-12">
            <div class="card text-center py-5">
                <i class="fas fa-door-open fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Bạn chưa được phân phòng</h4>
                <p class="text-muted">Vui lòng liên hệ quản trị viên để được phân phòng.</p>
            </div>
        </div>
    </div>

@else
    {{-- Form và danh sách sự cố --}}
    <div class="row">
        {{-- Form báo sự cố --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i> Báo cáo sự cố mới
                    </h5>
                </div>
                <div class="card-body d-flex flex-column">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('client.suco.store') }}" method="POST" enctype="multipart/form-data" class="flex-fill d-flex flex-column">
                        @csrf
                        <input type="hidden" name="phong_id" value="{{ $phong->id }}">

                        <div class="mb-3">
                            <label class="form-label">Sinh viên</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $sinhVien->ho_ten }} ({{ $sinhVien->ma_sinh_vien }})" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phòng</label>
                            <input type="text" class="form-control bg-light" 
                                   value="{{ $phong->ten_phong }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả sự cố</label>
                            <textarea name="mo_ta" class="form-control" rows="4" 
                                      placeholder="Nhập mô tả chi tiết..." required>{{ old('mo_ta') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh minh chứng (nếu có)</label>
                            <input type="file" name="anh" class="form-control" accept="image/*">
                        </div>

                        <div class="mt-auto">
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="fa fa-paper-plane"></i> Gửi báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Danh sách sự cố --}}
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i> Sự cố gần đây</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height:600px; overflow:auto;">
                        @if($dsSuCo->count())
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light position-sticky top-0" style="z-index:1;">
                                    <tr>
                                        <th class="fit text-center">ID</th>
                                        <th class="fit">Ảnh</th>
                                        <th class="fit">Ngày gửi</th>
                                        <th class="fit">Ngày hoàn thành</th>
                                        <th>Mô tả</th>
                                        <th class="fit">Giá tiền</th>
                                        <th class="fit">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($dsSuCo as $sc)
                                        <tr>
                                            <td class="text-center">{{ $sc->id }}</td>
                                            <td>
                                                <img src="{{ $sc->display_anh }}" 
                                                     alt="Ảnh sự cố" 
                                                     class="img-thumbnail" 
                                                     style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                            </td>
                                            <td>{{ $sc->ngay_gui?->format('d/m/Y') ?? '-' }}</td>
                                            <td>{{ $sc->ngay_hoan_thanh?->format('d/m/Y') ?? '-' }}</td>
                                            <td style="max-width:200px;">{{ $sc->mo_ta }}</td>
                                            <td>{{ $sc->payment_amount > 0 ? number_format($sc->payment_amount,0,',','.') . ' ₫' : '0 ₫' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $sc->trang_thai == 'Hoàn thành' ? 'success' : 'warning' }}">
                                                    {{ $sc->trang_thai }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-4 text-muted fst-italic">
                                <img src="https://dummyimage.com/120x80/eff3f9/9aa8b8&text=No+data" class="mb-2" alt="No data">
                                <div>Chưa có sự cố nào</div>
                            </div>
                        @endif
                    </div>

                    @if($dsSuCo->count())
                        <div class="mt-2 d-flex justify-content-center p-2">
                            {{ $dsSuCo->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
