@extends('admin.layouts.admin')

@section('title', 'Danh sách người dùng')

@section('content')
<div class="container mt-4">

    @push('styles')
    <style>
        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
         display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .page-title i {
            color: #4e54c8;
        }

        .page-subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
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

        .btn-dergin--info {
            background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
            box-shadow: 0 6px 16px rgba(14, 165, 233, .22);
        }

        .btn-dergin--info:hover {
            box-shadow: 0 10px 22px rgba(14, 165, 233, .32);
        }

        .btn-dergin--muted {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%);
            box-shadow: 0 6px 16px rgba(107, 114, 128, .22);
        }

        .btn-dergin--muted:hover {
            box-shadow: 0 10px 22px rgba(107, 114, 128, .32);
        }

        .btn-dergin--success {
            background: linear-gradient(135deg, #10b981 0%, #22c55e 100%);
            box-shadow: 0 6px 16px rgba(16, 185, 129, .22);
        }

        .btn-dergin--success:hover {
            box-shadow: 0 10px 22px rgba(16, 185, 129, .32);
        }

        .btn-dergin--warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            box-shadow: 0 6px 16px rgba(245, 158, 11, .22);
        }

        .btn-dergin--warning:hover {
            box-shadow: 0 10px 22px rgba(245, 158, 11, .32);
        }

        .btn-dergin.btn-sm {
            padding: .35rem .8rem;
            font-size: .75rem;
        }

        .user-table-wrapper {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: box-shadow 0.2s ease;
        }

        .user-table-wrapper:hover {
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .user-table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .user-table thead th {
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
            border: none;
            padding: 1rem 1rem;
            font-weight: 600;
            background: transparent;
        }

        .user-table tbody tr {
            background: #f9fafb;
            border-radius: 12px;
            transition: all .2s ease;
            border: 1px solid transparent;
        }

        .user-table tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(15, 23, 42, 0.1);
            background: #fff;
            border-color: #e5e7eb;
        }

        .user-table tbody td {
            border: none;
            vertical-align: middle;
            padding: 1.1rem 1rem;
            color: #374151;
        }

        .user-table tbody tr td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .user-table tbody tr td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        .font-weight-600 {
            font-weight: 600;
            color: #1f2937;
        }

        .filter-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 20px 24px;
            margin-bottom: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            transition: box-shadow 0.2s ease;
        }

        .filter-card:hover {
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }

        .filter-card label {
            font-weight: 600;
            color: #374151;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 0.65rem 1rem;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .form-control:focus {
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78, 84, 200, 0.1);
            outline: none;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .badge {
            font-size: .75rem;
            padding: .4rem .75rem;
            font-weight: 600;
            border-radius: 8px;
            display: inline-block;
        }

        .badge-info {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.2);
        }

        .alert {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        .pagination {
            margin-top: 1.5rem;
        }

        .pagination .page-link {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #4e54c8;
            margin: 0 4px;
            padding: 0.5rem 0.75rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background: #4e54c8;
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.2);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            border-color: #4e54c8;
            box-shadow: 0 4px 12px rgba(78, 84, 200, 0.3);
        }
    </style>
    @endpush

    <h4 class="page-title">
        <i class="fa fa-users"></i>
        Danh sách người dùng
    </h4>
    <p class="page-subtitle">Quản lý và theo dõi tất cả người dùng trong hệ thống</p>

    {{-- 🔔 Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
    </div>
    @endif

    <!-- Ô tìm kiếm -->
    <form method="GET" class="mb-3 search-bar">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') ?? '' }}" class="form-control"
                placeholder="Tìm kiếm theo tên, email...">
            <button type="submit" class="btn btn-outline-secondary">Tìm kiếm</button>
            <button type="button" class="btn btn-outline-primary" id="openFilterModalBtn">
                <i class="fa fa-filter mr-1"></i> Bộ lọc
            </button>

            @if (!empty(request('search')))
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Xóa</a>
            @endif
        </div>
    </form>

    <div class="d-flex gap-2 mb-3">
        <a href="{{ route('users.create') }}" class="btn btn-dergin btn-dergin--success">
            <i class="fa fa-plus"></i> Thêm người dùng
        </a>
    </div>

    {{-- 🧾 Bảng danh sách --}}
    <div class="user-table-wrapper">
        <div class="table-responsive">
            <table class="table align-middle user-table">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Quyền</th>
                        <th width="140">Ngày tạo</th>
                        <th class="text-end" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-center">{{ $user->id }}</td>
                        <td class="font-weight-600">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td class="text-center">
                            @if($user->roles->isNotEmpty())
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->ten_quyen }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Chưa gán</span>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="text-end">
                            <a href="{{ route('users.edit', $user->id) }}" 
                               class="btn btn-dergin btn-dergin--warning btn-sm" 
                               title="Sửa">
                                    <i class="fa fa-edit"></i>
                                </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Chưa có người dùng nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

    {{-- ✅ Phân trang --}}
        <div class="d-flex justify-content-center mt-3">
        {{ $users->onEachSide(1)->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- MODAL BỘ LỌC --}}
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="filterModalLabel">Bộ lọc người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form method="GET" id="filterForm">
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Tìm kiếm</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control" placeholder="Tên, email...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="small text-muted">Quyền</label>
                                <select name="role" class="form-control">
                                    <option value="">-- Tất cả --</option>
                                    @if(isset($roles))
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" @selected(request('role') == $role->id)>
                                                {{ $role->ten_quyen }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Xóa lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Mở modal bộ lọc người dùng (chạy được cho cả Bootstrap 4 và 5)
        (function() {
            document.addEventListener('DOMContentLoaded', function() {
                var btn = document.getElementById('openFilterModalBtn');
                if (!btn) return;

                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var modalEl = document.getElementById('filterModal');
                    if (!modalEl) return;

                    try {
                        if (window.bootstrap && bootstrap.Modal) {
                            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                            modal.show();
                        } else if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    } catch (err) {
                        if (window.$ && $('#filterModal').modal) {
                            $('#filterModal').modal('show');
                        }
                    }
                });
            });
        })();
    </script>
@endpush

@endsection
