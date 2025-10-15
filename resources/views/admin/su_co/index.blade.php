@extends('admin.layouts.admin')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="x_panel">
    <div class="x_title">
        <h2>Danh sách sự cố</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a href="{{ route('suco.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Thêm sự cố
                </a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Sinh viên</th>
                    <th>Phòng</th>
                    <th>Mô tả</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th width="150px">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suco as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->sinhVien->ten ?? '---' }}</td>
                    <td>{{ $item->phong->ten_phong ?? '---' }}</td>
                    <td>{{ Str::limit($item->mo_ta, 60) }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->ngay_gui)->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge 
                            @if($item->trang_thai == 'Tiếp nhận') bg-secondary
                            @elseif($item->trang_thai == 'Đang xử lý') bg-info
                            @elseif($item->trang_thai == 'Hoàn thành') bg-success
                            @else bg-warning
                            @endif">
                            {{ $item->trang_thai }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('suco.show', $item->id) }}" class="btn btn-sm btn-info" title="Xem chi tiết">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="{{ route('suco.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Cập nhật">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('suco.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Xóa sự cố này?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Chưa có sự cố nào được ghi nhận.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $suco->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
