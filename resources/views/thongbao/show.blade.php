@extends('admin.layouts.admin', ['noSidebar' => true])

@section('title', 'Chi tiết thông báo')

@section('content')
<div class="container-fluid py-4" style="max-width:100%;">

    {{-- Ảnh thông báo --}}
    @if (!empty($thongbao->anh))
        <div class="w-100 d-flex align-items-center justify-content-center mb-4">
            <img src="{{ asset('storage/' . $thongbao->anh) }}" 
                 alt="{{ $thongbao->tieuDe->ten_tieu_de ?? 'Ảnh thông báo' }}" 
                 width="300" class="img-thumbnail">
        </div>
    @endif

    {{-- Thông tin cơ bản --}}
    <table class="table table-bordered">
        <colgroup>
            <col width="30%">
            <col width="70%">
        </colgroup>
        <tbody>
            <tr>
                <th>Tiêu đề</th>
                <td>{{ $thongbao->tieuDe->ten_tieu_de ?? '-' }}</td>
            </tr>
            <tr>
                <th>Ngày đăng</th>
                <td>{{ $thongbao->ngay_dang ? \Carbon\Carbon::parse($thongbao->ngay_dang)->format('d/m/Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Đối tượng</th>
                <td>{{ $thongbao->doi_tuong ?? '-' }}</td>
            </tr>
            <tr>
                <th>Mức độ</th>
                <td>
                    @php    
                        $mucDo = $thongbao->mucDo->ten_muc_do ?? 'Khác';
                        $badgeColor = match(strtolower($mucDo)) {
                            'cao' => 'bg-danger text-white',
                            'trung bình' => 'bg-warning text-dark',
                            'thấp' => 'bg-success text-white',
                            default => 'bg-secondary text-white',
                        };
                    @endphp
                    <span class="badge {{ $badgeColor }}">{{ $mucDo }}</span>
                </td>
            </tr>
            <tr>
                <th>Phòng</th>
                <td>
                    @if($thongbao->phongs->count())
                        @foreach($thongbao->phongs as $phong)
                            <span class="badge bg-light text-dark border">{{ $phong->ten_phong }}</span>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Khu</th>
                <td>
                    @if($thongbao->khus->count())
                        @foreach($thongbao->khus as $khu)
                            <span class="badge bg-light text-dark border">{{ $khu->ten_khu }}</span>
                        @endforeach
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>File đính kèm</th>
                <td>
                    @if($thongbao->file)
                        <a href="{{ asset('storage/' . $thongbao->file) }}" download class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-download"></i> Tải xuống
                        </a>
                        <span class="badge bg-light text-dark border">
                            {{ pathinfo($thongbao->file, PATHINFO_BASENAME) }}
                        </span>
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <th>Nội dung</th>
                <td>{!! $thongbao->noi_dung ?? '-' !!}</td>
            </tr>
        </tbody>
    </table>

    <a href="{{ route('thongbao.index') }}" class="btn btn-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Quay lại danh sách
    </a>
</div>
@endsection
