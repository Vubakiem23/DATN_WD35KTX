@extends('admin.layouts.admin')

@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách hóa đơn điện nước</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('hoadon.import') }}" method="POST" enctype="multipart/form-data" class="mb-4">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Nhập từ Excel</button>
            </div>
        </div>
    </form>
    <table class="table table-bordered table-striped">
        <thead class="">
            <tr>
                <th>Phòng</th>
                <th>Điện cũ</th>
                <th>Điện mới</th>
                <th>Số điện đã dùng</th>
                <th>Nước cũ</th>
                <th>Nước mới</th>
                <th>Số nước đã dùng</th>
                <th>Đơn giá điện</th>
                <th>Đơn giá nước</th>
                <th>Thành tiền</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
       <tbody>
    @foreach($hoaDons as $hoaDon)
        <tr>
            <td>{{ $hoaDon->phong->ten_phong ?? 'Không rõ' }}</td>
            <td>{{ $hoaDon->so_dien_cu }}</td>
            <td>{{ $hoaDon->so_dien_moi }}</td>
            <td>{{ $hoaDon->so_dien_moi - $hoaDon->so_dien_cu }}</td>
            <td>{{ $hoaDon->so_nuoc_cu }}</td>
            <td>{{ $hoaDon->so_nuoc_moi }}</td>
            <td>{{ $hoaDon->so_nuoc_moi - $hoaDon->so_nuoc_cu }}</td>
            <td>{{ number_format($hoaDon->don_gia_dien, 0, ',', '.') }} VND</td>
            <td>{{ number_format($hoaDon->don_gia_nuoc, 0, ',', '.') }} VND</td>
            <td>{{ number_format($hoaDon->thanh_tien, 0, ',', '.') }} VND</td>

           <td> <form action="{{ route('hoadon.destroy', $hoaDon->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa hóa đơn này không?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger ">
        Xóa
    </button><a href="{{ route('hoadon.export_pdf', $hoaDon->id) }}" class="btn btn-primary ">
    Xuất PDF
</a>
<a href="{{ route('hoadon.export_excel_phong', $hoaDon->id) }}" 
   class="btn btn-success ">
    Xuất Excel
</a>
</form>
<td>
        </tr>
    @endforeach
</tbody>
    </table>
</div>
@endsection
