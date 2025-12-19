@extends('public.layouts.app')
@section('title', 'Thêm phản hồi sinh viên')
@section('content')
    <div class="content-section about-page">
        <div class="container">
            <h3 class="title">
                Thêm phản hồi sinh viên
            </h3>

            <div class="col-span-12">
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
            </div>

            <div class="mt-3">
                <form method="POST" action="{{ route('client.phan_hoi.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="tieu_de">Tiêu đề</label>
                        <input type="text" class="form-control" name="tieu_de" id="tieu_de"
                            placeholder="Vui lòng nhập..." required>
                    </div>
                    <div class="form-group">
                        <label for="noi_dung">Nội dung</label>
                        <textarea class="form-control" name="noi_dung" id="noi_dung" rows="10" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2 btn-sm">Gửi phản hồi</button>
                </form>
            </div>
        </div>
    </div>
@endsection
