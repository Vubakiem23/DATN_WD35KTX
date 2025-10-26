@extends('admin.layouts.admin')

@section('content')
    <div class="table table-bordered">
        <h3 class="page-title">📋 Chi tiết vi phạm</h3>

        <div class="card p-4 shadow-sm border-0">
            <p><strong>Mã vi phạm:</strong> {{ $violation->id }}</p>
            <p><strong>Tên sự cố:</strong> {{ $violation->ten_suco }}</p>
            <p><strong>Mô tả:</strong> {{ $violation->mota }}</p>
            <p><strong>Mức độ:</strong> {{ $violation->mucdo }}</p>

            @if ($violation->image)
                <p><strong>Hình ảnh:</strong></p>
                <img src="{{ asset('storage/' . $violation->image) }}" alt="Ảnh vi phạm" class="img-fluid rounded shadow-sm"
                    style="max-width: 400px;">
            @endif

            <a href="{{ route('vipham.index') }}" class="btn btn-secondary mt-3">← Quay lại danh sách</a>
        </div>
    </div>
@endsection
