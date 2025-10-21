@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
@section('content')
    <div class="container mt-4">
        <h3 class="page-title">📋 Danh sách sinh viên</h3>

        <!-- Ô tìm kiếm -->
        <form method="GET" class="mb-3 search-bar">
            <div class="input-group">
                <input type="text"
                       name="search"
                       value="{{ $keyword ?? '' }}"
                       class="form-control"
                       placeholder="Tìm kiếm sinh viên (mã SV, họ tên, lớp, ngành)">
                <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
                @if(!empty($keyword))
                    <a href="{{ route('sinhvien.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                @endif
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Danh sách các sinh viên</h4>
            <!-- Nút thêm sinh viên -->
            <a href="{{ route('sinhvien.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm sinh viên</a>
        </div>
        <div class="tab-content">
            <div class="row g-3">
                @foreach($sinhviens as $sv)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong>{{ $sv->ho_ten }}</strong>
                                <span class="font-weight-bold">{{ $sv->ma_sinh_vien }}</span>
                            </div>
                            @if(!empty($sv->anh_sinh_vien))
                                <img src="{{ asset('storage/'.$sv->anh_sinh_vien) }}" class="card-img-top"
                                     style="height:160px;object-fit:cover" alt="{{ $sv->ho_ten }}">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center"
                                     style="height:160px;background:#f8f9fa">
                                    {{-- inline SVG placeholder so image always shows even if no file --}}
                                    <svg width="80" height="60" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="2" fill="#e9ecef"/>
                                        <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="card-body">
                                <p class="mb-1"><strong>Mã sinh viên:</strong> {{ $sv->ma_sinh_vien }}</p>
                                <p class="mb-1"><strong>Ngày sinh:</strong>
                                    {{ !empty($sv->ngay_sinh) ? \Carbon\Carbon::parse($sv->ngay_sinh)->format('d/m/Y') : '-' }}
                                </p>
                                <p class="mb-1"><strong>Giới tính:</strong> {{ $sv->gioi_tinh ?? '-' }}</p>
                                <p class="mb-1"><strong>Trạng thái:</strong>
                                    @php
                                        $status = $sv->trang_thai_ho_so ?? 'Khác';
                                        $badge = match($status) {
                                            'Đã duyệt' => 'bg-success',
                                            'Chờ duyệt' => 'bg-warning',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $status }}</span>
                                </p>
                            </div>
                            <div class="card-footer d-flex gap-2">
                                <button type="button" data-id="{{ $sv->id }}"
                                        class="btn btn-sm flex-fill btn-secondary openModalBtn">
                                    Thông Tin
                                </button>
                                <a href="{{ route('sinhvien.edit', $sv->id) }}"
                                   class="btn btn-sm btn-warning flex-fill">Sửa</a>

                                <form action="{{ route('sinhvien.destroy', $sv->id) }}" method="POST"
                                      style="display:inline-block" class="mb-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-danger flex-fill"
                                            onclick="return confirm('Xác nhận xóa sinh viên này?')">
                                        Xóa
                                    </button>
                                </form>

                                @if(($sv->trang_thai_ho_so ?? '') !== 'Đã duyệt')
                                    <form action="{{ route('sinhvien.approve', $sv->id) }}" method="POST"
                                          style="display:inline-block" class="mb-0">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success flex-fill">Duyệt</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Phân trang -->
        <div class="d-flex justify-content-center mt-3">
            {{ $sinhviens->onEachSide(1)->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Thông tin sinh viên</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.openModalBtn').on('click', function () {
                let id = $(this).data('id');
                get_sinh_vien(id);
                $('#exampleModal').modal('show');
            });
        });


        async function get_sinh_vien(id) {
            let url = `{{ route('sinhvien.show.modal', ['id'=>':id']) }}`;
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'GET',
                async: false,
                success: function (res, textStatus) {
                    console.log(res);
                    const response = res.data ?? '';
                    renderSinhvien(response);
                },
                error: function (request, status, error) {
                    let data = JSON.parse(request.responseText);
                    alert(data.message);
                }
            });
        }

        function renderSinhvien(html) {
            $('#modalBody').html(html);
        }

    </script>
@endsection
