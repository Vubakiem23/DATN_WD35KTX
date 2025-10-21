@extends('admin.layouts.admin')

@section('title', 'Danh sách thông báo')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Danh sách thông báo</h3>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <a href="{{ route('thongbao.create') }}" class="btn btn-primary mb-3">+ Thêm thông báo</a>

    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Ngày đăng</th>
                <th>Đối tượng</th>
                <th>Phòng</th>
                <th>Khu</th>
                <th>Ảnh</th>
                <th width="160">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($thongbaos as $tb)
            <tr>
                <td>{{ $tb->id }}</td>
                <td>
                    @if(strlen($tb->tieu_de) > 20)
                        {{ Str::limit($tb->tieu_de, 20, '...') }}
                        <a href="{{ route('thongbao.show', $tb->id) }}">Xem chi tiết</a>
                    @else
                        {{ $tb->tieu_de }}
                    @endif
                </td>
                <td>
                    @if(strlen($tb->noi_dung) > 30)
                        {{ Str::limit($tb->noi_dung, 30, '...') }}  
                    @else
                        {{ $tb->noi_dung }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($tb->ngay_dang)->format('d/m/Y') }}</td>
                <td>{{ $tb->doi_tuong }}</td>
                <td>{{ $tb->phong->ten_phong ?? 'Chưa có phòng' }}</td>
                <td>
                    @if($tb->phong)
                        {{ $tb->phong->khu }}
                    @else
                        <span class="text-danger">Chưa có khu</span>
                    @endif
                </td>
                <td>
                    @if($tb->anh)
                        <img src="{{ asset('storage/' . $tb->anh) }}" alt="Ảnh thông báo" width="80">
                    @else
                        Không có
                    @endif
                </td>
                <td>
                    <a href="{{ route('thongbao.show', $tb->id) }}" class="btn btn-info btn-sm">Xem</a>
                    <a href="{{ route('thongbao.edit', $tb->id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    <form action="{{ route('thongbao.destroy', $tb->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Bạn chắc chắn muốn xóa thông báo này?')" class="btn btn-danger btn-sm">Xóa</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Không có thông báo nào.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div>
        {{ $thongbaos->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
