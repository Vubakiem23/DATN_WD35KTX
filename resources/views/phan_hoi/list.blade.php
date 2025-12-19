@extends('admin.layouts.admin')
@section('title', 'Danh sách phản hồi sinh viên')
@section('content')
    <div class="container">
        <h3 class="title">
            Danh sách phản hồi sinh viên
        </h3>

        <div class="col-span-12">
            @if(session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <div class="mt-3">
            <table class="table table-hover">
                <colgroup>
                    <col width="50px">
                    <col width="20%">
                    <col width="30%">
                    <col width="x">
                    <col width="10%">
                    <col width="200px">
                </colgroup>
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Sinh viên</th>
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
                            @php
                                $sinhVien = \App\Models\User::where('id', $phanHoi->sinh_vien_id)->first();
                            @endphp
                            {{ $sinhVien->email }} / {{ $sinhVien->name }}
                        </td>
                        <td>
                            <a href="{{ route('admin.phan_hoi.show', $phanHoi) }}">{{ $phanHoi->tieu_de }}</a>
                        </td>
                        <td>
                            <div class="text-truncate">
                                {{ $phanHoi->noi_dung }}
                            </div>
                        </td>
                        <td> {{ $phanHoi->trang_thai == 1 ? 'Đã xác nhận' : 'Chờ xác nhận' }}</td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a class="btn btn-primary btn-sm"
                                   href="{{ route('admin.phan_hoi.show', $phanHoi) }}">Xem</a>

                                <form action="{{ route('admin.phan_hoi.delete', $phanHoi) }}" method="POST"
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
