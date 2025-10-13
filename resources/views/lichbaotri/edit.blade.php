@extends('admin.layouts.admin')

@section('title', 'Sửa lịch bảo trì')

@section('content')
<div class="container">
    <h2>Sửa lịch bảo trì</h2>

    {{-- Debug --}}
    <p>ID lịch bảo trì: {{ $lichBaoTri->id ?? 'NULL' }}</p>

    <form action="{{ route('lichbaotri.update', $lichBaoTri) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group mb-3">
            <label for="tai_san_id">Tài sản</label>
            <select name="tai_san_id" class="form-control" required>
                @foreach($taiSan as $ts)
                    <option value="{{ $ts->id }}" {{ $lichBaoTri->tai_san_id == $ts->id ? 'selected' : '' }}>
                        {{ $ts->ten_tai_san }} (Phòng: {{ $ts->phong->ten_phong ?? 'Chưa gán' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="ngay_bao_tri">Ngày bảo trì</label>
            <input type="date" name="ngay_bao_tri" value="{{ $lichBaoTri->ngay_bao_tri }}" class="form-control" required>
        </div>

        <div class="form-group mb-3">
            <label for="ngay_hoan_thanh">Ngày hoàn thành</label>
            <input type="date" name="ngay_hoan_thanh" value="{{ $lichBaoTri->ngay_hoan_thanh }}" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label for="mo_ta">Mô tả</label>
            <textarea name="mo_ta" class="form-control" rows="3">{{ $lichBaoTri->mo_ta }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('lichbaotri.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection
