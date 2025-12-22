@extends('public.layouts.app')
@section('title', 'Danh sách phản hồi sinh viên')
@section('content')
    <div class="content-section about-page">
        <div class="container">
            <h3 class="title">
                Danh sách phản hồi sinh viên
            </h3>

            <div class="d-flex justify-content-end align-items-center">
                <a href="{{ route('client.phan_hoi.create') }}" class="btn btn-primary btn-sm">Thêm phản hồi mới</a>
            </div>

            <div class="mt-3">
                <table class="table table-hover">
                    <colgroup>
                        <col width="50px">
                        <col width="30%">
                        <col width="x">
                        <col width="10%">
                        <col width="200px">
                    </colgroup>
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Tiêu đề</th>
                        <th scope="col">Nội dung</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($phanHois as $phanHoi)
                        <tr>
                            <th scope="row">{{ $loop->index + 1 }}</th>
                            <td>
                                <a href="{{ route('client.phan_hoi.show', $phanHoi) }}">{{ $phanHoi->tieu_de }}</a>
                            </td>
                            <td>
                                <div class="text-truncate">
                                    {{ $phanHoi->noi_dung }}
                                </div>
                            </td>
                            <td> 
                                @if($phanHoi->trang_thai == 1)
                                    <span class="badge bg-success">Đã xử lý</span>
                                @elseif($phanHoi->trang_thai == 2)
                                    <span class="badge bg-danger">Đã từ chối</span>
                                @else
                                    <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <a class="btn btn-info btn-sm"
                                       href="{{ route('client.phan_hoi.show', $phanHoi) }}">Xem</a>

                                    @if($phanHoi->trang_thai == 0)
                                        <a class="btn btn-primary btn-sm"
                                           href="{{ route('client.phan_hoi.edit', $phanHoi) }}">Sửa</a>
                                    @endif

                                    <form action="{{ route('client.phan_hoi.delete', $phanHoi) }}" method="POST"
                                          class="form-delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.getElementsByClassName('form-delete');

            for (let i = 0; i < forms.length; i++) {
                forms[i].addEventListener('submit', function (e) {
                    if (!confirm('Bạn có chắc chắn muốn xóa phản hồi này không?')) {
                        e.preventDefault();
                    }
                });
            }
        });

    </script>
@endsection
