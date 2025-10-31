@extends('admin.layouts.admin')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

@section('title', 'Danh sách thông báo')

@section('content')
<div class="container mt-4">
    <h3 class="page-title">📢 Danh sách thông báo</h3>

    {{-- Ô tìm kiếm nhanh --}}
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm (tiêu đề, nội dung, phòng, khu, đối tượng)">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            @if (!empty(request('search')))
            <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#filterModal">
                <i class="fa fa-filter mr-1"></i> Bộ lọc
            </button>
        </div>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Danh sách các thông báo</h4>
        <a href="{{ route('thongbao.create') }}" class="btn btn-primary mb-3 btn-add">+ Thêm thông báo</a>
    </div>

    {{-- Thông báo thành công --}}
    @if (session('success'))
    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    {{-- Bảng danh sách --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @php
            $perPage = $thongbaos->perPage();
            $currentPage = $thongbaos->currentPage();
            $sttBase = ($currentPage - 1) * $perPage;
            @endphp

            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle table-sv">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" style="width:60px">STT</th>
                            <th style="min-width:180px">Tiêu đề</th>
                            <th style="min-width:200px">Nội dung</th> {{-- thêm cột nội dung --}}
                            <th style="width:100px">Ảnh</th>
                            <th style="width:130px">Ngày đăng</th>
                            <th style="width:130px">Đối tượng</th>
                            <th style="width:120px">Mức độ</th>
                            <th style="min-width:120px">Phòng</th>
                            <th style="min-width:120px">Khu</th>
                            <th style="width:120px">File</th>
                            <th class="text-end" style="width:180px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($thongbaos as $tb)
                        <tr class="sv-row-main">
                            <td class="text-center">{{ $sttBase + $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $tb->tieuDe->ten_tieu_de ?? '---' }}</td>

                            {{-- Nội dung rút gọn --}}
                            <td>
                                {{ \Illuminate\Support\Str::limit(strip_tags($tb->noi_dung ?? ''), 20, '...') }}
                                <a href="#" class="openModalBtn" data-id="{{ $tb->id }}">Xem thêm</a>
                            </td>

                            {{-- Ảnh --}}
                            <td>
                                @if ($tb->anh)
                                <img src="{{ Storage::url($tb->anh) }}" style="height:60px;width:60px;object-fit:cover;border-radius:5px;" alt="Ảnh #{{ $tb->id }}">
                                @else
                                <div style="height:60px;width:60px;background:#f8f9fa;display:flex;align-items:center;justify-content:center;border-radius:5px;">
                                    <svg width="30" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="24" height="24" rx="2" fill="#e9ecef" />
                                        <path d="M3 15L8 9L13 15L21 6" stroke="#adb5bd" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                @endif
                            </td>

                            {{-- Các cột còn lại giữ nguyên --}}
                            <td>{{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}</td>
                            <td>{{ $tb->doi_tuong ?? '---' }}</td>
                            <td>
                                <span class="badge 
                @if(($tb->mucDo->ten_muc_do ?? '') === 'Cao') badge-danger 
                @elseif(($tb->mucDo->ten_muc_do ?? '') === 'Trung bình') badge-warning 
                @else badge-secondary @endif">
                                    {{ $tb->mucDo->ten_muc_do ?? '---' }}
                                </span>
                            </td>
                            <td>{{ $tb->phongs->pluck('ten_phong')->join(', ') ?: '---' }}</td>
                            <td>{{ $tb->khus->pluck('ten_khu')->join(', ') ?: '---' }}</td>
                            <td>
                                @if($tb->file)
                                <a href="{{ Storage::url($tb->file) }}" target="_blank" class="text-primary">
                                    <i class="fa fa-download"></i> Tải
                                </a>
                                @else
                                <span class="text-muted">Không có</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <button type="button" data-id="{{ $tb->id }}" class="btn btn-sm btn-secondary openModalBtn">Xem</button>
                                    <a href="{{ route('thongbao.edit', $tb->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                                    <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận xóa thông báo này?')">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Không có thông báo nào.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-3">
        {{ $thongbaos->onEachSide(1)->links() }}
    </div>
</div>

{{-- MODAL BỘ LỌC --}}
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bộ lọc thông báo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="GET">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Tìm nhanh</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tiêu đề, nội dung...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small text-muted">Đối tượng</label>
                            <select name="doi_tuong" class="form-control">
                                <option value="">-- Tất cả --</option>
                                <option value="Sinh viên" @selected(request('doi_tuong')=='Sinh viên' )>Sinh viên</option>
                                <option value="Cán bộ" @selected(request('doi_tuong')=='Cán bộ' )>Cán bộ</option>
                                <option value="Khác" @selected(request('doi_tuong')=='Khác' )>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small text-muted">Mức độ</label>
                            <select name="muc_do" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @isset($mucdos)
                                @foreach ($mucdos as $md)
                                <option value="{{ $md->id }}" @selected(request('muc_do')==$md->id)>{{ $md->ten_muc_do }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small text-muted">Phòng</label>
                            <select name="phong_id" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @isset($phongs)
                                @foreach ($phongs as $p)
                                <option value="{{ $p->id }}" @selected(request('phong_id')==$p->id)>{{ $p->ten_phong }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="small text-muted">Khu</label>
                            <select name="khu" class="form-control">
                                <option value="">-- Tất cả --</option>
                                @isset($khus)
                                @foreach ($khus as $k)
                                <option value="{{ $k }}" @selected(request('khu')==$k)>{{ $k }}</option>
                                @endforeach
                                @endisset
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Ngày đăng từ</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-muted">Đến</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('thongbao.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL XEM CHI TIẾT --}}
<div class="modal fade" id="thongBaoModal" tabindex="-1" aria-labelledby="thongBaoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title">Chi tiết thông báo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                {{-- nội dung ajax load --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- Script Ajax Modal --}}
<script>
    $(document).ready(function() {
        $('.openModalBtn').on('click', function() {
            let id = $(this).data('id');
            let url = `{{ route('thongbao.show', ':id') }}`.replace(':id', id);
            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    $('#modalBody').html(res);
                    $('#thongBaoModal').modal('show');
                },
                error: function(err) {
                    $('#modalBody').html('<p class="text-danger text-center py-3">Không thể tải chi tiết thông báo.</p>');
                    $('#thongBaoModal').modal('show');
                }
            });
        });
    });
</script>

@push('styles')
<style>
    .badge {
        border-radius: 10rem;
        padding: 0.35rem 0.6rem;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .table-sv tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s;
    }

    .btn-group .btn {
        margin-right: 4px;
    }
</style>
@endpush
@endsection