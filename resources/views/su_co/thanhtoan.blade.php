@extends('admin.layouts.admin')

@section('content')
<div class="x_panel">
    <div class="x_title">
        <h2>🧾 Thanh toán sự cố</h2>
        <div class="clearfix"></div>
    </div>

    <div class="x_content">
        <form action="{{ route('suco.luuThanhtoan') }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Sinh viên</th>
                        <th>Phòng</th>
                        <th>Mô tả sự cố</th>
                        <th>Giá tiền (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sucos as $suco)
                        <tr>
                            <td>{{ $suco->id }}</td>
                            <td>{{ $suco->sinhVien->ten ?? '' }}</td>
                            <td>{{ $suco->phong->ten_phong ?? '' }}</td>
                            <td>{{ $suco->mo_ta }}</td>
                            <td>
                                <input type="number" 
                                       name="payment[{{ $suco->id }}]" 
                                       class="form-control" 
                                       value="{{ $suco->payment_amount ?? 0 }}" 
                                       min="0">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="submit" class="btn btn-success mt-2">Cập nhật và tạo hóa đơn</button>
        </form>
    </div>
</div>
@endsection
