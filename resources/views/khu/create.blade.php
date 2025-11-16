@extends('admin.layouts.admin')
@section('title','Thêm khu')

@section('content')
<div class="container mt-4">

    @push('styles')
    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
        }

        .form-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
        }

        .btn-dergin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .5rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: .85rem;
            border: none;
            color: #fff;
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            box-shadow: 0 6px 16px rgba(78, 84, 200, .22);
            transition: transform .2s ease, box-shadow .2s ease;
            text-decoration: none;
        }

        .btn-dergin:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(78, 84, 200, .32);
            color: #fff;
        }

        .btn-dergin i {
            font-size: .8rem;
        }

        .btn-dergin--success {
            background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
        }

        .btn-dergin--muted {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
        }
    </style>
    @endpush

    <h4 class="page-title mb-0">Thêm khu</h4>
    <p class="text-muted mb-3">Tạo khu mới cho ký túc xá.</p>

    <div class="form-card">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('khu.store') }}" method="POST">
            @include('khu._form')
        </form>
    </div>
</div>
@endsection



