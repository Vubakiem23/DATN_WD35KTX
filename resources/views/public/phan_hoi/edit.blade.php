@extends('public.layouts.app')
@section('title', 'Sửa phản hồi')
@section('content')
    <div class="content-section about-page">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="title mb-0">
                    Sửa phản hồi: #{{ $phanHoi->id }}
                </h3>
                <a href="{{ route('client.phan_hoi.list') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Quay lại
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('client.phan_hoi.update', $phanHoi) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label for="tieu_de">Tiêu đề</label>
                            <input type="text" class="form-control" id="tieu_de" placeholder="Vui lòng nhập..."
                                   value="{{ $phanHoi->tieu_de }}" name="tieu_de" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="noi_dung">Nội dung</label>
                            <textarea class="form-control" name="noi_dung" id="noi_dung" rows="10"
                                      required>{{ $phanHoi->noi_dung }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save me-1"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
